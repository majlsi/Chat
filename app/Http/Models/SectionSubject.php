<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of SectionSubject
 *
 * @author yasser.mohamed
 */
class SectionSubject extends Model
{
    protected $fillable = array('section_id', 'subject_id', 'date');

    // DEFINE RELATIONSHIPS --------------------------------------------------
    public function section()
    {
        return $this->belongsTo('Section');
    }

    // DEFINE RELATIONSHIPS --------------------------------------------------
    public function subject()
    {
        return $this->belongsTo('Subject');
    }
}