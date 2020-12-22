<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->assertTrue((bool)$category->is_active);
    }
}
