<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use DatabaseMigrations;
    /**
     * @var Video
     */
    protected $video;

    /** @var array */
    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $categories = factory(Category::class)->create();
        $genres = factory(Genre::class)->create();
        $genres->categories()->sync($categories->id);
        $this->video = factory(Video::class)->create([
            'opened' => false
        ]);
        $this->sendData = [
            'title' => 'title',
            'description' => 'rescription',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'categories_id' => [$categories->id],
            'genres_id' => [$genres->id]
        ];
    }
}