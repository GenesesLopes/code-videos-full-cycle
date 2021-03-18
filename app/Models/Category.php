<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Uuid;

class Category extends Model
{
    use SoftDeletes, Uuid;

    //
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'is_active' => 'boolean'
    ];


    protected $dates = ['deleted_at'];

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}