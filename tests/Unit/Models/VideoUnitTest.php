<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Traits\{
    Uuid,
    UploadFiles
};
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class VideoUnitTest extends TestCase
{

    /**
     *
     * @var Video
     */
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video;
    }

    public function testFilable()
    {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'video_file',
            'thumb_file',
            'banner_file',
            'trailer_file'
        ];

        $this->assertEquals(
            $fillable,
            $this->video->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class, UploadFiles::class];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->video->getDates());
        }
        $this->assertCount(count($dates), $this->video->getDates());
    }

    public function testRatingList()
    {
        $ratingList = ['L', '10', '12', '14', '16', '18'];
        $this->assertEquals($ratingList, Video::RATING_LIST);
    }

    public function testMethodsRelations()
    {
        $methods = ['categories', 'genres'];
        $methodsClass = get_class_methods(Video::class);
        foreach ($methods as $method) {
            $this->assertContains($method, $methodsClass);
        }
    }
}