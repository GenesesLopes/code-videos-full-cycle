<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase\Models;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    /**
     *
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category;
    }
    
    public function testFilable()
    {
        $fillable = ['name', 'description', 'is_active'];
        
        $this->assertEquals(
            $fillable,
            $this->category->getFillable()
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
        
        $this->assertFalse($this->category->incrementing);
    }

    public function testKeyTypes()
    {
        
        $keyType = 'string';
        $this->assertEquals($keyType,$this->category->getKeyType());
    }

    public function testDates()
    {
        
        $dates = ['deleted_at','created_at','updated_at'];
        foreach($dates as $date){
            $this->assertContains($date,$this->category->getDates());
        }
        $this->assertCount(count($dates),$this->category->getDates());
    }
}
