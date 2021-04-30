<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Arr;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Traits\{
    TestResources,
    TestSaves,
    TestValidations
};
use Illuminate\Http\UploadedFile;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestSaves, TestResources;

    private $FieldSerialized = [
        "id",
        "title",
        "description",
        "year_launched",
        "opened",
        "rating",
        "duration",
        "video_file",
        "thumb_file",
        "banner_file",
        "trailer_file",
        "deleted_at",
        "created_at",
        "updated_at",
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ]
    ];
    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->FieldSerialized
                    ],
                    'meta' => [],
                    'links' => []
                ]
            );
        $this->assertResource($response, VideoResource::collection(collect([$this->video])));

        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function testShow()
    {

        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->FieldSerialized
            ])
            ->assertJsonFragment($this->video->toArray());
        $this->assertResource($response, new VideoResource($this->video));
        $this->assertIfFilesUrlExists($this->video, $response);
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
        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);

        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + [
                    'opened' => true
                ],
                'test_data' => $testData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => Video::RATING_LIST[1]
                ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]]
            ]
        ];
        foreach ($data as $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'data' => $this->FieldSerialized
            ]);
            $this->assertResource($response, new VideoResource(
                Video::find($response->json('data.id'))
            ));
            // $this->assertIfFilesUrlExists($this->video, $response);

            $this->assertHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertHasGenre(
                $response->json('data.id'),
                $value['send_data']['genres_id'][0]
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'data' => $this->FieldSerialized
            ]);
            $this->assertResource($response, new VideoResource(
                Video::find($response->json('data.id'))
            ));
            // $this->assertIfFilesUrlExists($this->video, $response);

            $this->assertHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );
            $this->assertHasGenre(
                $response->json('data.id'),
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
            ->assertJsonStructure([
                'data' => $this->FieldSerialized
            ]);;
    }



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
    //     $categoriesId = factory(Category::class, 3)->create()->pluck('data.id')->toArray();
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
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('data.id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genreId],
    //             'categories_id' => [$categoriesId[1], $categoriesId[2]]
    //         ]
    //     );

    //     $this->assertDatabaseMissing('category_video', [
    //         'category_id' => $categoriesId[0],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[1],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('category_video', [
    //         'category_id' => $categoriesId[2],
    //         'video_id' => $response->json('data.id')
    //     ]);
    // }

    // public function testSyncGenres()
    // {
    //     $genres = factory(Genre::class, 3)->create();
    //     $genresId = $genres->pluck('data.id')->toArray();
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
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $response = $this->json(
    //         'PUT',
    //         route('videos.update', ['video' => $response->json('data.id')]),
    //         $this->sendData + [
    //             'genres_id' => [$genresId[1], $genresId[2]],
    //             'categories_id' => [$categoryId]
    //         ]
    //     );

    //     $this->assertDatabaseMissing('genre_video', [
    //         'genre_id' => $genresId[0],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genresId[1],
    //         'video_id' => $response->json('data.id')
    //     ]);

    //     $this->assertDatabaseHas('genre_video', [
    //         'genre_id' => $genresId[2],
    //         'video_id' => $response->json('data.id')
    //     ]);
    // }


}