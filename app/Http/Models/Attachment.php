<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of AttachmentModel
 *
 * @author Ghada
 */
class Attachment extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['attachemnt_name','attachemnt_url'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'attachments';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'attachemnt_name' => 'required', 
                    'attachemnt_url' => 'required',                   
                );
            case 'update':
                return array(
                    'attachemnt_name' => 'required',
                    'attachemnt_url' => 'required',
                );
        }
    }
}
