<?php

namespace App;


use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
   
    use HasApiTokens, Notifiable;

    protected $table = 'staff';
    protected $primaryKey = 'nip';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nip','name', 'email', 'password','leader'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function projects(){
        return $this->hasMany('App\Project','project_manager','nip');
    }

    public function tasks(){
        return $this->hasMany('App\task','handled_by','nip');
    }
}
