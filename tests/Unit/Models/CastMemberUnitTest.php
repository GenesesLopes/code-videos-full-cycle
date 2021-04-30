<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CastMemberUnitTest extends TestCase
{

    /**
     *
     * @var CastMember
     */
    private $cast_member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast_member = new CastMember;
    }

    public function testFilable()
    {
        $fillable = ['name', 'type'];

        $this->assertEquals(
            $fillable,
            $this->cast_member->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];
        $cast_memberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $cast_memberTraits);
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->cast_member->incrementing);
    }

    public function testKeyTypes()
    {

        $keyType = 'string';
        $this->assertEquals($keyType, $this->cast_member->getKeyType());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->cast_member->getDates());
        }
        $this->assertCount(count($dates), $this->cast_member->getDates());
    }
}