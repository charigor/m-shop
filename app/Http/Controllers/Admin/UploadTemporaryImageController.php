<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadTemporaryImageController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = $image->getClientOriginalName();
            $folder = uniqid('image-', true);
            $image->storeAs('/images/tmp/'.$folder, $fileName);

            return response()->json(['folder' => $folder, 'path' => '/images/tmp/'.$folder.'/'.$fileName]);
        }

        return '';
    }
}
