<?php

namespace Sinarajabpour1998\Attachment;

use Sinarajabpour1998\Attachment\Facades\AttachmentFacade;
use Sinarajabpour1998\Attachment\Helpers\AttachmentHelper;
use Sinarajabpour1998\Attachment\View\Components\Attachment as AttachmentComponent;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\ImageManipulation;
use Illuminate\Support\Facades\Validator;

class AttachmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        AttachmentFacade::shouldProxyTo(AttachmentHelper::class);
    }


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/views','attachment');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/config/attachment.php', 'attachment');
        $this->publishes([
            __DIR__.'/config/attachment.php' =>config_path('attachment.php'),
            __DIR__.'/views/' => resource_path('views/vendor/attachment'),
        ], 'attachment');

        $this->loadViewComponentsAs('', [
            AttachmentComponent::class
        ]);

        ImageManipulator::defineVariant(
            'thumbnail',
            ImageManipulation::make(function (Image $image) {
                $image->fit(200, 200);
            })->outputPngFormat()
        );

        Validator::extend('attachment_check_disk_is_public', function ($attribute, $value, $parameters, $validator) {
            if(
                request()->has('disk')
                &&
                (request('file_type') == 'image' || request('file_type') == 'video')
                &&
                config('filesystems.disks.' . request('disk') . '.visibility') == 'private'
            ) {
                return false;
            }
            return true;
        }, config('attachment.check_disk_is_public_message'));

        Validator::extend('attachment_disk_not_found', function ($attribute, $value, $parameters, $validator) {
            if(
                request()->has('disk')
                &&
                ! in_array(request('disk'), array_keys(config('filesystems.disks')))
            ) {
                return false;
            }
            return true;
        }, config('attachment.disk_not_found_message'));
    }
}
