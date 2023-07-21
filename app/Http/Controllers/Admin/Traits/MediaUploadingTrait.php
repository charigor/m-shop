<?php


namespace App\Http\Controllers\Admin\Traits;


use Illuminate\Http\Request;

trait MediaUploadingTrait
{
    public function saveMedia(Request $request)
    {

        $path = storage_path('app/public/tmp/uploads/');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = now()->timestamp.'_'.uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);
        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}

