<?php

namespace App\Repositories\Collection;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Collection;
use App\Repositories\Account\IAccountRepository;
use App\Repositories\Auth\IUserRepository;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;

class CollectionRepository extends BaseRepository implements ICollectionRepository
{
    private $userRepo, $accountRepo;

    public function __construct(IUserRepository $userRepo, IAccountRepository $accountRepo)
    {
        $this->userRepo = $userRepo;
        $this->accountRepo = $accountRepo;
    }

    public function getCollectionsList(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }


        $collections = $this->getCollections($size, $query);

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Collections list generated', $this->formatCollections($collections), 'collections', false);
    }

    private function getCollectionById($id)
    {
        return DB::table('collections')
            ->select("*")
            ->where('collections.id', '=', $id)
            ->first();
    }

    private function getCollections($size, $query)
    {
        return DB::table('collections')
            ->select("*")
            ->where('collections.comments', 'LIKE', "%{$query}%")
            ->orderBy('collections.id')
            ->paginate($size)
            ->toArray();
    }

    private function formatCollections($collections)
    {
        $data = $collections['data'];
        $i = 0;
        foreach ($data as $item) {
            $collections['data'][$i] = $this->formatCollection($item);
            $i++;
        }

        return $collections;
    }


    public function createCollection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collector_user_id' => 'required|numeric',
            'retailer_user_id' => 'required|numeric',
            'comments' => 'required|string|min:2',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $collectionValidator = $this->validateCollection(
            $request->get('collector_user_id'),
            $request->get('retailer_user_id'),
            $request->get('comments'),
            $request->get('amount')
        );

        if ($collectionValidator['isInvalid']) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                $collectionValidator['errors'],
            );
        }

        $collection = $this->saveCollection(
            $request->get('collector_user_id'),
            $request->get('retailer_user_id'),
            $request->get('comments'),
            $request->get('amount')
        );


        if (is_null($collection) || empty($collection)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE);
        }

        $transaction = $this->accountRepo->createTransaction(
            $this->accountRepo->getUserAccountByUserID($request->get('retailer_user_id'))->account_no,
            MAIN_ACCOUNT,
            $request->get('amount')
        );

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_CREATE,
            'Collection created',
            $this->formatCollection($collection),
            'collection',
            true
        );
    }

    private function formatCollection($collection)
    {
        $result = [];
        $result['id'] = $collection->id;
        $result['collector'] = $this->userRepo->getuserById($collection->collector_user_id);
        $result['retailer'] = $this->userRepo->getuserById($collection->retailer_user_id);
        $result['comments'] = $collection->comments;
        $result['amount'] = $collection->amount;

        return $result;
    }

    private function saveCollection($collector_user_id, $retailer_user_id, $comments, $amount)
    {
        $collection = new Collection(
            [
                'invoice_id' => uniqid('#'),
                'collector_user_id' => $collector_user_id,
                'retailer_user_id' => $retailer_user_id,
                'comments' => $comments,
                'amount' => $amount
            ]
        );

        $collection->save();

        return $collection;
    }

    private function validateCollection($collector_user_id, $retailer_user_id, $comments, $amount)
    {
        $errors = [];
        $isInvalid = false;

        $colletor = $this->userRepo->getuser($collector_user_id);
        if (is_null($colletor) || empty($colletor)) {
            array_push($errors, "Collector not found!");
            $isInvalid = true;
        }

        $retailer = $this->userRepo->getuser($retailer_user_id);
        if (is_null($retailer) || empty($retailer)) {
            array_push($errors, "Retailer not found!");
            $isInvalid = true;
        }

        if (is_null($comments) || empty($comments)) {
            array_push($errors, "Comments not found!");
            $isInvalid = true;
        }

        if (is_null($amount) || empty($amount) || $amount <= 0 || $amount > 99999999) {
            array_push($errors, "Invalid amount!");
            $isInvalid = true;
        }

        return [
            'isInvalid' => $isInvalid,
            'errors' => $errors,
        ];
    }

    public function showCollection($id)
    {
        $collection = $this->getCollectionById($id);
        if (is_null($collection) || empty($collection)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_NOT_FOUND, "collection doesn't exist", ["invalid collection id"]);
        }

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'Collection data',
            $this->formatCollection($collection),
            'collection',
            false
        );
    }


    public function updateCollection($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'collector_user_id' => 'required|numeric',
            'retailer_user_id' => 'required|numeric',
            'comments' => 'required|string|min:2',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $collectionValidator = $this->validateCollection(
            $request->get('collector_user_id'),
            $request->get('retailer_user_id'),
            $request->get('comments'),
            $request->get('amount')
        );
        if ($collectionValidator['isInvalid']) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                $collectionValidator['errors'],
            );
        }

        $collection = $this->updateCollectionValues(
            $id,
            $request->get('collector_user_id'),
            $request->get('retailer_user_id'),
            $request->get('comments'),
            $request->get('amount')
        );
        if (is_null($collection) || empty($collection)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }


        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'Collection successfully updated',
            $this->formatCollection($collection),
            'collection',
            true
        );
    }

    private function updateCollectionValues($id, $collector_user_id, $retailer_user_id, $comments, $amount)
    {
        try {
            DB::table('collections')
                ->where('collections.id', $id)
                ->update(
                    [
                        'collector_user_id' => $collector_user_id,
                        'retailer_user_id' => $retailer_user_id,
                        'comments' => $comments,
                        'amount' => $amount,
                    ]
                );
        } catch (\Exception $e) {
            return null;
        }

        return $this->getCollectionById($id);
    }

    public function destroyCollection($id)
    {
        $status = $this->deleteCollection($id);

        if ($status == false) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_NOT_FOUND, "Collection doesn't exist", []);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Collection successfully deleted', null, "collection", false);
    }

    private function deleteCollection($id)
    {
        return DB::table('collections')
            ->where('collections.id', $id)
            ->delete();
    }
}
