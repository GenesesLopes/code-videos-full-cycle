<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
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
            'opened' => false,
            'thumb_file' => 'thumb.jpg',
            'banner_file' => 'banner.jpg',
            'video_file' => 'video.mp4',
            'trailer_file' => 'trailer.mp4'
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

    protected function assertIfFilesUrlExists(Video $video, TestResponse $response)
    {
        $fileFields = Video::$fileFields;
        $data = $response->json('data');
        $data = array_key_exists(0, $data) ? $data[0] : $data;
        foreach ($fileFields as $field) {
            $file = $video->{$field};
            $this->assertEquals(
                \Storage::url($video->relativeFilePath($file)),
                $data[$field . '_url']
            );
        }
    }
}