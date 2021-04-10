<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Api\BasicCrudController;
use App\Http\Resources\CategoryResource;

class CategoryController extends BasicCrudController
{

    private $rules =  [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'description' => 'nullable'
    ];


    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resource()
    {
        return CategoryResource::class;
    }
}