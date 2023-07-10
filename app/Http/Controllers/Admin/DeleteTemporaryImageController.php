<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteTemporaryImageController extends Controller
{
    public function __invoke($folder)
    {
        Storage::deleteDirectory('images/tmp/'.$folder);
        return '';
    }
}
