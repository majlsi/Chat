<?php

namespace Helpers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class UploadHelper
{

    public static function uploadFile(Request $request, $fileName, $subDirectory)
    {
        if ($request->$fileName) {
            $file = $request->file($fileName);
            $path = $request->get('path');

            $destinationPath = public_path() . "/uploads" . $subDirectory. $path;
            $name = preg_replace('/\s+/', '', $file->getClientOriginalName());
            $filename = time() . '_' . $name;
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            if ($file->move($destinationPath, $filename)) {
                $filePath = "uploads" . $subDirectory .$path . '/' . $filename;
                return $filePath;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

}