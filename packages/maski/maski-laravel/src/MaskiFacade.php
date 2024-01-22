<?php

namespace Maski\Maski;

use Illuminate\Support\Facades\Facade as BaseFacade;

class MaskiFacade extends BaseFacade
{
    protected static function getFacadeAccessor(): string
    {
        return MaskiService::class;
    }
}
