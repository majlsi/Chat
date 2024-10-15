<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleRight extends Model
{

    use SoftDeletes;

    protected $fillable = ['role_id', 'right_id'];

    public function role()
    {
        return $this->belongsTo('Models\Role');
    }

    public function right()
    {
        return $this->belongsTo('Models\Right');
    }
}
