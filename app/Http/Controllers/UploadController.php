<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Helpers\UploadHelper;
use Validator;
use Lang;

class UploadController extends Controller {

   
    public function __construct() {

    }

    public function uploadImage(Request $request) {
        $validator = Validator::make($request->all(), [
                    'file' => 'required|mimes:jpeg,jpg,png'
        ]);
        if ($validator->fails()) {
            return response()->json(["error_code" => 3, "message" => $validator->errors()->all()], 400);
        }
        
        return UploadHelper::uploadFile($request, 'file', '/subDirectory');
        
    }

    public function uploadFile(Request $request){
        $data = $request->all();
        if (isset($data['file'])) {
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:jpeg,jpg,png,pdf,txt,doc,docx,odt,xls,xlsx,ppt,pptx,avi,mov,mp4,wmv,rtf|max:' . config('attachment.file_size')
            ]);
            if ($validator->fails()) {
                return response()->json(["error" => $validator->errors()->all()], 400);
            }
            return response()->json(["url" => UploadHelper::uploadFile($request, 'file','/chat')], 200);
        } else {
            return response()->json(['error' => Lang::get('validation.custom.file.not-found',[],'en')], 400);
        }
    } 
}
