<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase\Models;
use Tests\TestCase;

class GenreTest extends TestCase
{

    /**
     *
     * @var Genre
     */
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre;
    }

    public function testFilable()
    {
        $fillable = ['name', 'is_active'];

        $this->assertEquals(
            $fillable,
            $this->genre->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }

    public function testIncrementing()
    {

        $this->assertFalse($this->genre->incrementing);
    }

    public function testKeyTypes()
    {

        $keyType = 'string';
        $this->assertEquals($keyType, $this->genre->getKeyType());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->genre->getDates());
        }
        $this->assertCount(count($dates), $this->genre->getDates());
    }

    public function testCasts()
    {
        $casts = [
            'is_active' => 'boolean'
        ];
        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testMethodsRelations()
    {
        $method = 'genres';
        $methodsClass = get_class_methods(Category::class);
        $this->assertTrue(in_array($method, $methodsClass));
    }
}