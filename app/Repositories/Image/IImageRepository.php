<?php


namespace App\Repositories\Image;


interface IImageRepository
{
    public function storeImage($postedImage);
}
