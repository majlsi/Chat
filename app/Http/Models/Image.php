<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of ImageModel
 *
 * @author Eman
 */
class Image extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['original_image_url','image_url','cropper_top','cropper_left','cropper_width','cropper_height','file_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'images';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'original_image_url' => 'required',
                    'image_url' => 'required',
                    
                );
            case 'update':
                return array(
                    'original_image_url' => 'required',
                    'image_url' => 'required',
                );
        }
    }


}
