<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Http\Controllers\Api\BasicCrudController;

class VideoController extends BasicCrudController
{
    protected $rule;

    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
    }

    protected function rulesUpdate()
    {
    }
}