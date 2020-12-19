<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class Genus extends Model
{
    use SoftDeletes, Uuid;
     //
     protected $fillable = [
        'name',
        'is_active'
    ];

    public $increment = false;
    protected $keyType = 'string';


    protected $dates = ['deleted_at'];
}
