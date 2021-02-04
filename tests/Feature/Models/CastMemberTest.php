<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;

    /** @var CastMember */
    private $cast_member;

    protected function setUp(): void 
    {
        parent::setUp();
        $this->cast_member = factory(CastMember::class)->create();
        $this->cast_member->refresh();
    }
    

    public function testList()
    {
        

        $this->assertNotNull($this->cast_member);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'type',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            array_keys($this->cast_member->toArray())
        );
    }

    public function testCreate()
    {
        $data = [
            [
                'name' => 'nome 01',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'nome 02',
                'type' => CastMember::TYPE_ACTOR
            ],
        ];
        foreach ($data as $value) {
            $this->cast_member = CastMember::create($value);
            foreach ($value as $key => $value_data) {
                if ($key !== 'id')
                    $this->assertEquals($value_data, $this->cast_member->{$key});
                else
                    $this->assertTrue(Uuid::isValid($this->cast_member->id));
            }
        }
    }

    public function testUpdate()
    {
        $this->cast_member = factory(CastMember::class)->create([
            'name' => 'test_Name',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $data = [
            'name' => 'nome qualquer',
            'type' => CastMember::TYPE_ACTOR
        ];
        $this->cast_member->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $this->cast_member->{$key});
        }
    }

    public function testDestroy()
    {
        $id = $this->cast_member->id;
        $this->cast_member->delete();
        $this->assertNull(CastMember::find($id));
        $this->assertNotNull(CastMember::onlyTrashed()->get());
    }
}
