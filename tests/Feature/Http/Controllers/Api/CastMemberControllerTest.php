<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations;
    use TestSaves;

    private $castMember;

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
            ->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['category' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJsonFragment($this->castMember->toArray());
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
            'created_at',
            'updated_at'
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
            'created_at',
            'updated_at'
        ]);

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
