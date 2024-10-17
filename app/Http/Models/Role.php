<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model{


    use SoftDeletes;
    
    protected $fillable  = ['role_name' , 'role_name_ar'];

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'role_name' => 'required',
                    'role_name_ar' => 'required'
                   
                );
            case 'update':
                return array(
                    'role_name' => 'required',
                    'role_name_ar' => 'required'
                );
        }
    }
    
   public function users(){
       return $this->hasMany('Models\User');
   }
   
   public function rights(){
       return $this->hasMany('Models\RoleRight');
   }
}

