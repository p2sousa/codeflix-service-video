<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations;
    use TestSaves;
    use TestResources;

    private $castMember;
    private $serializedFields = [
        'id',
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = factory(CastMember::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => [],
            ]);

        $resource = CastMemberResource::collection(collect([$this->castMember]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationRulePost()
    {
        $this->assertInvalidationInStoreAction(['type' => ''], 'required');
        $this->assertInvalidationInStoreAction(['name' => ''], 'required');
        $this->assertInvalidationInStoreAction(['type' => 'a'], 'in');

        $this->assertInvalidationInStoreAction(
            ['name' => str_repeat('a', 256)],
            'max.string',
            ['max' => 255]
        );
    }

    public function testInvalidationRulePut()
    {
        $this->assertInvalidationInStoreAction(['type' => ''], 'required');
        $this->assertInvalidationInStoreAction(['name' => ''], 'required');
        $this->assertInvalidationInStoreAction(['type' => 'a'], 'in');
        $this->assertInvalidationInUpdateAction(
            ['name' => str_repeat('a', 256)],
            'max.string',
            ['max' => 255]
        );
    }

    public function testStore()
    {
        $response = $this->assertStore(
            [
                'name' => 'teste',
                'type' => CastMember::TYPE_ACTOR
            ],
            [
                'name' => 'teste',
                'type' => CastMember::TYPE_ACTOR,
                'deleted_at' => null,
            ]
        );

        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $this->assertStore(
            [
                'name' => 'teste',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'teste',
                'type' => CastMember::TYPE_DIRECTOR,
                'deleted_at' => null,
            ]
        );

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $this->assertResource($response, $resource);
    }

    public function testUpdate()
    {
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);

        $data = [
            'name' => 'teste',
            'type' => CastMember::TYPE_ACTOR
        ];

        $response = $this->assertUpdate($data,$data + ['deleted_at' => null]);

        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $this->assertResource($response, $resource);

        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $this->assertUpdate($data, array_merge($data, ['type' => CastMember::TYPE_DIRECTOR]));
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(204);

        $this->assertEmpty($response->getContent());

    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->castMember->id]);
    }
    
}
