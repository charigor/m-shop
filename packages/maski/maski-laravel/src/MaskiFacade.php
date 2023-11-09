<?php

namespace Maski\Maski;

use Illuminate\Support\Facades\Facade as BaseFacade;
use Maski\Maski\MaskiService;

class MaskiFacade extends BaseFacade
{
    protected static function getFacadeAccessor(): string
    {
        return MaskiService::class;
    }
}
