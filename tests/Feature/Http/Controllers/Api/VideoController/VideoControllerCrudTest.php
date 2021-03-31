<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;

use Tests\Traits\{
    TestSaves,
    TestValidations
};
use Illuminate\Http\UploadedFile;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestSaves;

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }

    public function testShow()
    {

        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpendedField()
    {
        $data = [
            'opened' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [123]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [123]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'genres_id' => [$genre->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }


    public function testSaveWithoutFiles()
    {
        $categories = factory(Category::class)->create();
        $genres = factory(Genre::class)->create();
        $genres->categories()->sync($categories->id);
        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$categories->id],
                    'genres_id' => [$genres->id]
                ],
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + [
                    'opened' => true,
                    'categories_id' => [$categories->id],
                    'genres_id' => [$genres->id]
                ],
                'test_data' => $this->sendData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => Video::RATING_LIST[1],
                    'categories_id' => [$categories->id],
                    'genres_id' => [$genres->id]
                ],
                'test_data' => $this->sendData + ['rating' => Video::RATING_LIST[1]]
            ]
        ];
        foreach ($data as $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
            $this->assertHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);

            $this->assertHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );
            $this->assertHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );
        }
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


    public function testDestroy()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));
        $response
            ->assertStatus(204);

        $this->assertNull(Video::find($this->video->id));
        $this->video->restore();
        $response = $this->json('GET', route('videos.show', ['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    // public function testRolbackStore()
    // {
    //     $controller = \Mockery::mock(VideoController::class)
    //         ->makePartial()
    //         ->shouldAllowMockingProtectedMethods();
    //     $controller->shouldReceive('validate')
    //         ->withAnyArgs()
    //         ->andReturn($this->sendData);

    //     $controller->shouldReceive('rulesStore')
    //         ->withAnyArgs()
    //         ->andReturn([]);

    //     $controller->shouldReceive('handleRelations')
    //         ->once()
    //         ->withAnyArgs()
    //         ->andThrow(new TestException());
    //     /** @var MockInterface */
    //     $request = \Mockery::mock(Request::class);
    //     $request->shouldReceive('get')
    //         ->withAnyArgs()
    //         ->andReturnNull();

    //     $hasError = false;
    //     try {
    //         $controller->store($request);
    //     } catch (TestException $e) {
    //         $this->assertCount(1, Video::all());
    //         $hasError = true;
    //     }

    //     $this->assertTrue($hasError);
    // }

    // public function testRolbackUpdate()
    // {
    //     $controller = \Mockery::mock(VideoController::class)
    //         ->makePartial()
    //         ->shouldAllowMockingProtectedMethods();
    //     $controller->shouldReceive('findOrFail')
    //         ->withAnyArgs()
    //         ->andReturn($this->video);
    //     $controller->shouldReceive('validate')
    //         ->withAnyArgs()
    //         ->andReturn($this->sendData);

    //     $controller->shouldReceive('rulesUpdate')
    //         ->withAnyArgs()
    //         ->andReturn([]);

    //     /** @var MockInterface */
    //     $request = \Mockery::mock(Request::class);
    //     $request->shouldReceive('get')
    //         ->withAnyArgs()
    //         ->andReturnNull();

    //     $controller->shouldReceive('handleRelations')
    //         ->once()
    //         ->withAnyArgs()
    //         ->andThrow(new TestException());
    //     $hasError = false;
    //     try {
    //         $controller->update($request, 1);
    //     } catch (TestException $e) {
    //         $this->assertCount(1, Video::all());
    //         $hasError = true;
    //     }
    //     $this->assertTrue($hasError);
    // }

    // public function testSyncCategories()
    // {
    //     $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
    //     $genre = factory(Genre::class)->create();
    //     $genre->categories()->sync($categoriesId);
    //     $genreId = $genre->id;

    //     $response = $this->json(
    //         'POST',
    //         $this->routeStore(),
    //         $this->sendData + [
    //             'genres_id' => [$genreId],
    //             'categories_id' => [$categoriesId[0]]
    //         ]
    //     );

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[0],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genreId],
    //             'categories_id' => [$categoriesId[1], $categoriesId[2]]
    //         ]
    //     );

    //     $this->assertDatabaseMissing('category_video', [
    //         'category_id' => $categoriesId[0],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[1],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[2],
    //         'video_id' => $response->json('id')
    //     ]);
    // }

    // public function testSyncGenres()
    // {
    //     $genres = factory(Genre::class, 3)->create();
    //     $genresId = $genres->pluck('id')->toArray();
    //     $categoryId = factory(Category::class)->create()->id;
    //     $genres->each(function ($genre) use ($categoryId) {
    //         $genre->categories()->sync($categoryId);
    //     });

    //     $response = $this->json(
    //         'POST',
    //         $this->routeStore(),
    //         $this->sendData + [
    //             'genres_id' => [$genresId[0]],
    //             'categories_id' => [$categoryId]
    //         ]
    //     );

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genresId[0],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genresId[1], $genresId[2]],
    //             'categories_id' => [$categoryId]
    //         ]
    //     );

    //     $this->assertDatabaseMissing('genre_video', [
    //         'genre_id' => $genresId[0],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genresId[1],
    //         'video_id' => $response->json('id')
    //     ]);

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genresId[2],
    //         'video_id' => $response->json('id')
    //     ]);
    // }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function routeDestroy()
    {
        return route('videos.destroy', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}