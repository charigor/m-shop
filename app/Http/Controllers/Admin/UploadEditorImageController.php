<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UploadEditorImageController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->hasFile('upload')) {
            $image = $request->file('upload');
            $fileName = $image->getClientOriginalName();
            $folder = uniqid('image-editor-', true);
            Storage::disk('public')->put('/images-editor/tmp/'.$folder.'/'.$fileName, File::get($image));

            return response()->json(['url' => Storage::url('images-editor/tmp/'.$folder.'/'.$fileName)]);
        }

        return '';
    }
}
