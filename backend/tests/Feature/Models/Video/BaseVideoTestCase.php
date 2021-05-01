<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoTestCase extends TestCase
{

    use DatabaseMigrations;

    /** @var Video */
    protected $video;

    /** @var array */
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'title 2',
            'description' => 'description 2',
            'year_launched' => 2020,
            'rating' => Video::RATING_LIST[3],
            'duration' => 90
        ];
        $this->video = factory(Video::class)->create();
        $this->video->refresh();
    }
}