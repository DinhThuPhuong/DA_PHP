<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Cloudinary;
use App\Services\CloudinaryAdapter;

class CloudinaryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Cloudinary::class, function ($app) {
            return new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
                'url' => [
                    'secure' => config('cloudinary.secure')
                ]
            ]);
        });

        $this->app->singleton(CloudinaryAdapter::class, function ($app) {
            return new CloudinaryAdapter($app->make(Cloudinary::class));
        });
    }
} 