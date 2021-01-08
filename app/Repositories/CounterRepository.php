<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator;
use App\Counter;
use Illuminate\Support\Facades\Storage;

class CounterRepository implements ICounterRepository
{
    public function getInvoiceList()
    {
        return Counter::join('users','users.id', '=', 'counter.user_id')
            ->join('roles_v_users','users.id', '=', 'roles_v_users.user_id')
            ->join('roles','roles.id', '=', 'roles_v_users.role_id')
            ->select('counter.*', 'users.username', 'roles.rolename')
            ->orderBy('counter.id', 'desc')
            ->simplePaginate(25);
    }

    public function saveCounterEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'type' => 'required|string|max:100',
            'customer_name' => 'string',
            'customer_address' => 'string',
            'customer_phone' => 'string',
            'amount' => 'numeric',
            'invoice_photo' => 'string|max:10000000',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $imageUrl = $this->storeImage($request->get('invoice_photo'));

        $counter = new Counter([
            'invoice_id' => uniqid('#'),
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'customer_name' => $request->get('customer_name'),
            'customer_address' => $request->get('customer_address'),
            'customer_phone' => $request->get('customer_phone'),
            'amount' => $request->get('amount'),
            'invoice_photo' => $imageUrl,
        ]);


        $counter = $counter->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice successfully created'
        ], 201);
    }


    // stores image
    private function storeImage($encodedImage)
    {
        $image = $encodedImage;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid('image_') . '.png';
        Storage::disk('images')->put($imageName, base64_decode($image));
        $imageUrl = asset('images/'.$imageName);

        return $imageUrl;
    }
}

