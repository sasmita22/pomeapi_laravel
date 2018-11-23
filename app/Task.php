<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id',
		'name',
		'status',
		'type',
		'project_structure',
		'deadline_at',
		'finished_at',
		'handled_by',
    ];    
}
