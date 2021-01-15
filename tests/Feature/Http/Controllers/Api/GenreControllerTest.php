<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Lang;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;


    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([Lang::get('validation.required', ['attribute' => 'name'])]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }
    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genre.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genre.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('genre.store', []));

        $this->assertInvalidationRequired($response);

        $response = $this->json('POST', route('genre.store', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $genre = factory(Genre::class)->create();

        $response = $this->json('PUT', route('genre.update', ['genre' => $genre->id]));

        $this->assertInvalidationRequired($response);

        $response = $this->json(
            'PUT',
            route(
                'genre.update',
                ['genre' => $genre->id]
            ),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('genre.store'), [
            'name' => 'test'
        ]);

        $genre = Genre::find($response->json('id'));

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genre.store'), [
            'name' => 'test',
            'is_active' => false,
        ]);

        $response->assertJsonFragment([
            'is_active' => false
        ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $response = $this->json('PUT', route('genre.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true
        ]);

        $genre = Genre::find($response->json('id'));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true,
                'name' => 'test'
            ]);

        $response = $this->json('PUT', route('genre.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true
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
}
