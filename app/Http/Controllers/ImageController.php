<?php


namespace App\Http\Controllers;


use App\Repositories\Image\IImageRepository;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    private $imageRepo;

    public function __construct(IImageRepository $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    public function showImageList(Request $request)
    {
        return $this->imageRepo->getImageList($request);
    }


    public function store(Request $request)
    {
        return $this->imageRepo->storeImageRequest($request);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        return $this->imageRepo->updateImageRequest($request, $id);
    }


    public function destroy($id)
    {
        return $this->imageRepo->deleteImageRequest($id);
    }
}
