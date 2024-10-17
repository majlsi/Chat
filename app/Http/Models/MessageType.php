<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of MessageTypeModel
 *
 * @author Ghada
 */
class MessageType extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['message_type_name'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'message_types';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'message_type_name' => 'required',                    
                );
            case 'update':
                return array(
                    'message_type_name' => 'required',
                );
        }
    }
}
