<?php


namespace App\Repositories\Collection;


use Illuminate\Http\Request;

interface ICollectionRepository
{
    public function getCollectionsList(Request $request);

    public function createCollection(Request $request);

    public function showCollection($id);

    public function updateCollection($request, $id);

    public function destroyCollection($id);
}
