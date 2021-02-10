<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    /** @var Video */
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create();
        $this->video->refresh();
    }


    public function testList()
    {

        $this->assertNotNull($this->video);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            array_keys($this->video->toArray())
        );
    }

    public function testCreate()
    {
        $data = [
            [
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ],
            [
                'title' => 'title 2',
                'description' => 'description 2',
                'year_launched' => 2020,
                'rating' => Video::RATING_LIST[3],
                'duration' => 87
            ],
        ];
        foreach ($data as $value) {
            $this->video = Video::create($value);
            foreach ($value as $key => $value_data) {
                if ($key !== 'id')
                    $this->assertEquals($value_data, $this->video->{$key});
                else
                    $this->assertTrue(Uuid::isValid($this->video->id));
            }
        }
    }

    public function testUpdate()
    {
        $this->video = factory(Video::class)->create([
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ]);
        $data = [
            'title' => 'title 2',
            'description' => 'description 2',
            'year_launched' => 2020,
            'rating' => Video::RATING_LIST[3],
            'duration' => 87
        ];
        $this->video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $this->video->{$key});
        }
    }

    public function testDestroy()
    {
        $id = $this->video->id;
        $this->video->delete();
        $this->assertNull(Video::find($id));
        $this->assertNotNull(Video::onlyTrashed()->get());
    }
}