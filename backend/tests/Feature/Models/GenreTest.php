<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;


    public function testList()
    {
        factory(Genre::class, 1)->create();
        $geners = Genre::all();

        $this->assertCount(1, $geners);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            array_keys($geners->first()->toArray())
        );
    }

    public function testCreate()
    {
        $genre = Genre::create(['name' => 'genre 01']);
        $genre->refresh();
        $this->assertEquals('genre 01', $genre->name);
        $this->assertTrue($genre->is_active);
        $this->assertTrue(Uuid::isValid($genre->id));
    }

    public function testUpdate()
    {
        /**
         * @var Genre
         */
        $genre = factory(Genre::class)->create([
            'name' => 'test_Name',
            'is_active' => false
        ]);
        $data = [
            'name' => 'nome qualquer',
            'is_active' => true
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDestroy()
    {
        /**
         * @var Genre
         */
        $genre = factory(Genre::class)->create();
        $genre->refresh();
        $id = $genre->id;
        $genre->delete();
        $this->assertNull(Genre::find($id));
    }
}
