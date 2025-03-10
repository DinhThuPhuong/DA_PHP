<?php

namespace App\Services;

use Cloudinary\Cloudinary;

class CloudinaryAdapter
{
    protected $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function upload($file, $options = [])
    {
        $defaultOptions = [
            'folder' => 'uploads',
            'overwrite' => true,
            'resource_type' => 'auto'
        ];

        $options = array_merge($defaultOptions, $options);

        $response = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            $options
        );

        // Chuyển đổi ApiResponse thành mảng
        return (array) $response;
    }

    public function delete($publicId, $options = [])
    {
        $defaultOptions = [
            'resource_type' => 'image'
        ];

        $options = array_merge($defaultOptions, $options);

        $response = $this->cloudinary->uploadApi()->destroy(
            $publicId,
            $options
        );

        // Chuyển đổi ApiResponse thành mảng
        return (array) $response;
    }
} 