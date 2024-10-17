<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Section
 *
 * @author yasser.mohamed
 */
class Section extends Model
{
    protected $fillable = array('name', 'code');

    public function sectionSubjects()
    {
        return $this->hasMany('Models\SectionSubject');
    }
}