<?php

namespace Tests\Feature\Models\Video;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Models\Video\BaseVideoTestCase;

class VideoCrudTest extends BaseVideoTestCase
{

    public function testList()
    {

        $this->assertNotNull($this->video);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'video_file',
                'thumb_file',
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
                'duration' => 90,
                'opened' => true
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

    public function testCreateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testRolbackCreate()
    {
        try {
            $hasError = false;
            Video::create([
                'title' => 'title',
                'description' => 'rescription',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $e) {
            $this->assertCount(1, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testUpdateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $this->video->update($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $this->assertHasCategory($this->video->id, $category->id);
        $this->assertHasGenre($this->video->id, $genre->id);
    }

    public function testRolbackUpdate()
    {
        $hasError = false;
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;
        try {
            $video->update([
                'title' => 'title',
                'description' => 'rescription',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $e) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }
    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

    public function testHandleRelations()
    {
        Video::handleRelations($this->video, []);
        $this->assertCount(0, $this->video->categories);
        $this->assertCount(0, $this->video->genres);

        $category = factory(Category::class)->create();
        Video::handleRelations($this->video, [
            'categories_id' => [$category->id]
        ]);
        $this->video->refresh();
        $this->assertCount(1, $this->video->categories);

        $genre = factory(Genre::class)->create();
        Video::handleRelations($this->video, [
            'genres_id' => [$genre->id]
        ]);
        $this->video->refresh();
        $this->assertCount(1, $this->video->genres);

        $this->video->categories()->delete();
        $this->video->genres()->delete();

        Video::handleRelations($this->video, [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $this->video->refresh();
        $this->assertCount(1, $this->video->categories);
        $this->assertCount(1, $this->video->genres);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        Video::handleRelations($this->video, [
            'categories_id' => [$categoriesId[0]]
        ]);
        $this->video->refresh();
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $this->video->id
        ]);

        Video::handleRelations($this->video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ]);
        $this->video->refresh();

        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $this->video->id
        ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $this->video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $this->video->id
        ]);
    }

    public function testSyncGenres()
    {
        $genresId = factory(Genre::class, 3)->create()->pluck('id')->toArray();
        Video::handleRelations($this->video, [
            'genres_id' => [$genresId[0]]
        ]);
        $this->video->refresh();
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $this->video->id
        ]);

        Video::handleRelations($this->video, [
            'genres_id' => [$genresId[1], $genresId[2]]
        ]);
        $this->video->refresh();

        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $this->video->id
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $this->video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $this->video->id
        ]);
    }
}