<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Subject
 *
 * @author yasser.mohamed
 */
class Subject extends Model
{
    protected $fillable = array('name');

    public function sectionSubjects()
    {
        return $this->hasMany('SectionSubject');
    }
}