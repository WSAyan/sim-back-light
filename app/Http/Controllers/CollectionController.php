<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Collection\ICollectionRepository;

class CollectionController extends Controller
{
    private $collectionRepo;

    public function __construct(ICollectionRepository $collectionRepo)
    {
        $this->collectionRepo = $collectionRepo;
    }

    public function showCollectionsList(Request $request)
    {
        return $this->collectionRepo->getCollectionsList($request);
    }

    public function store(Request $request)
    {
        return $this->collectionRepo->createCollection($request);
    }

    public function show($id)
    {
        return $this->collectionRepo->showCollection($id);
    }

    public function update(Request $request, $id)
    {
        return $this->collectionRepo->updateCollection($request, $id);
    }

    public function destroy($id)
    {
        return $this->collectionRepo->destroyCollection($id);
    }
}
