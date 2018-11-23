<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    public $timestamps = false;  


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'invoice_id',
		'notes',
		'total',
		'status',
		'due_date',
		'project'
    ];

}
