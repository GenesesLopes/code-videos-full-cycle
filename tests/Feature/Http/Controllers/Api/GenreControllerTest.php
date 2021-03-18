<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Exceptions\TestException;
use App\Http\Controllers\Api\GenreController;
use App\Models\{
    Genre,
    Category
};
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;
use Lang;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     *
     * @var Genre
     */
    private $genre;

    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('genre.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genre.show', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'categories_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256)
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
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

    public function testStore()
    {
        $data = [
            'name' => 'test'
        ];
        $response = $this->assertStore(
            $data + ['categories_id' => [$this->category->id]],
            $data + ['is_active' => true, 'deleted_at' => null]
        );
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $this->assertHasCategory($response->json('id'), $this->category->id);
        $data = [
            'name' => 'test',
            'is_active' => false
        ];

        $this->assertStore(
            $data + ['categories_id' => [$this->category->id]],
            $data + ['is_active' => true, 'deleted_at' => null]
        );
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $data = [
            'name' => 'test',
            'is_active' => true
        ];

        $response = $this->assertUpdate(
            $data + ['categories_id' => [$this->category->id]],
            $data + ['deleted_at' => null]
        );

        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
        $this->assertHasCategory($response->json('id'), $this->category->id);
    }

    protected function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryId
        ]);
    }

    public function testDestroy()
    {

        $genre = factory(Genre::class)->create();
        $response = $this->json('DELETE', route('genre.destroy', ['genre' => $genre->id]));
        $response
            ->assertStatus(204);

        $this->assertNull(Genre::find($genre->id));
        $genre->restore();
        $response = $this->json('GET', route('genre.show', ['genre' => $genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testRolbackStore()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test'
            ]);

        $controller->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->withAnyArgs()
            ->andThrow(new TestException());
        $request = \Mockery::mock(Request::class);
        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $e) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRolbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);
        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn([
                'name' => 'test'
            ]);

        $controller->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $request = \Mockery::mock(Request::class);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->withAnyArgs()
            ->andThrow(new TestException());
        $hasError = false;
        try {
            $controller->update($request, 1);
        } catch (TestException $e) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST', $this->routeStore(), $sendData);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->json(
            'PUT',
            route('genre.update', ['genre' => $response->json('id')]),
            $sendData
        );

        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $response->json('id')
        ]);

        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $response->json('id')
        ]);
    }

    protected function routeStore()
    {
        return route('genre.store');
    }

    protected function routeUpdate()
    {
        return route('genre.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}