<?php

namespace Maski\Maski;

use Stevebauman\Location\Facades\Location;

class MaskiService
{
    public function getSomeLogic()
    {
        if ($position = Location::get('188.163.43.250')) {
            dd($position->countryName);
            // Successfully retrieved position.
            //            echo $position->countryName;
        } else {
            // Failed retrieving position.
        }

    }
}
