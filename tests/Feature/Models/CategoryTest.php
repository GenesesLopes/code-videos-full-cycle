<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;


    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();

        $this->assertCount(1, $categories);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            array_keys($categories->first()->toArray())
        );
    }

    public function testCreate()
    {
        $category = Category::create(['name' => 'test 01']);
        $category->refresh();
        $this->assertEquals('test 01', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertTrue(Uuid::isValid($category->id));
    }

    public function testUpdate()
    {
        /**
         * @var Category
         */
        $category = factory(Category::class)->create([
            'description' => 'test_description',
            'is_active' => false
        ])->first();
        $data = [
            'name' => 'nome qualquer',
            'description' => 'description qualquer',
            'is_active' => true
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDestroy()
    {

        /**
         * @var Category
         */
        $category = factory(Category::class)->create();
        $category->refresh();
        $id = $category->id;
        $category->delete();
        $this->assertNull(Category::find($id));
    }
}
