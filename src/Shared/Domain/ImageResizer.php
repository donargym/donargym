<?php

namespace App\Shared\Domain;

use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class ImageResizer
{
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager();
    }

    public function resizeByChangingWidth(
        $originalImageLocation,
        $newLocation,
        int $newWidth,
        bool $removeOriginalImage = true
    ): void {
        $image = $this->imageManager->make($originalImageLocation);
        $image->resize(
            $newWidth,
            null,
            function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );
        $image->save($newLocation);
        if ($removeOriginalImage) {
            unlink($originalImageLocation);
        }
    }

    public function resizeByChangingHeight(
        $originalImageLocation,
        $newLocation,
        int $newHeight,
        bool $removeOriginalImage = true
    ): void {
        $image = $this->imageManager->make($originalImageLocation);
        $image->resize(
            null,
            $newHeight,
            function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );
        $image->save($newLocation);
        if ($removeOriginalImage) {
            unlink($originalImageLocation);
        }
    }
}
