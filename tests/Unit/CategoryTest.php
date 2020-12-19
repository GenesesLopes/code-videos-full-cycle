<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    public function testFilable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals(
            $fillable,
            $category->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits,$categoryTraits);
    }

    public function testIncrementing()
    {
        $category = new Category();
        $this->assertFalse($category->incrementing);
    }

    public function testKeyTypes()
    {
        $category = new Category();
        $keyType = 'string';
        $this->assertEquals($keyType,$category->getKeyType());
    }

    public function testDates()
    {
        $category = new Category();
        $dates = ['deleted_at','created_at','updated_at'];
        foreach($dates as $date){
            $this->assertContains($date,$category->getDates());
        }
        $this->assertCount(count($dates),$category->getDates());
    }
}
