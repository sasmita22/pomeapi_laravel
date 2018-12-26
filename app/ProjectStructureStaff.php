<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectStructureStaff extends Model
{
    protected $table = 'project_structure_staff';
    protected $primaryKey = 'id_project_structure';
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
	    'id_project_structure',
        'staff'
    ];  
}
