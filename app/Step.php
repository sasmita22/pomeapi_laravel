<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $table = 'steps';
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
        'deskripsi',
		'type',
    ];  


    public function projects()
    {
        return $this->belongsToMany('App\Project','project_structures','id','step');
    }   
}
