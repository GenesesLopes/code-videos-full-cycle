<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\{
    TestResources,
    TestSaves,
    TestValidations
};


class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    /**
     * @var CastMember
     */
    private $cast_member;

    private $serializedFields = [
        'id',
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast_member = factory(CastMember::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'meta' => [],
                'links' => []
            ]);
        $resource = CastMemberResource::collection(collect([$this->cast_member]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {

        $response = $this->get(route('cast_members.show', ['cast_member' => $this->cast_member->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ])
            ->assertJsonFragment($this->cast_member->toArray());
        $resource = new CastMemberResource(CastMember::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);


        $data = [
            'type' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore()
    {
        $data = [
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_DIRECTOR
            ]
        ];
        foreach ($data as $value) {
            $response = $this->assertStore($value, $value + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
            $resource = new CastMemberResource(CastMember::find($response->json('data.id')));
            $this->assertResource($response, $resource);
        }
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);
        $resource = new CastMemberResource(CastMember::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->cast_member->id]));
        $response
            ->assertStatus(204);

        $this->assertNull(CastMember::find($this->cast_member->id));
        $this->assertNotNull(CastMember::onlyTrashed()->get());
        $this->cast_member->restore();
        $response = $this->json('GET', route('cast_members.show', ['cast_member' => $this->cast_member->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->cast_member->id]);
    }

    protected function routeDestroy()
    {
        return route('cast_members.destroy', ['cast_member' => $this->cast_member->id]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}