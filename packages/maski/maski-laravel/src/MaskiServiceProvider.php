<?php
namespace Maski\Maski;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
class MaskiServiceProvider extends BaseServiceProvider
{
    public function boot()
    {

    }
    public function register()
    {
        $this->app->singleton(MaskiService::class,function(){
            return new MaskiService();
        });
    }
}
