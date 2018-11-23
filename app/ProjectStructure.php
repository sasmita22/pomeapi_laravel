<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectStructure extends Model
{
    protected $table = 'project_structures';
    protected $primaryKey = 'id';
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id',
		'id_project',
		'step'
    ];  


}
