<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestUuid;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;
    use TestUuid;

    public function testList()
    {
        factory(CastMember::class, 1)->create();
        $castMembers = CastMember::all();
        $this->assertCount(1, $castMembers);
        $castMemberKey = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'type',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            $castMemberKey
        );
    }

    public function testCreate()
    {
        $castMember = CastMember::create([
            'name' => 'test',
        ]);

        $castMember->refresh();

        $this->assertTrue($this->isUuid($castMember->id));
        $this->assertEquals('test', $castMember->name);
        $this->assertEquals(2, $castMember->type);

        $castMember = CastMember::create([
            'name' => 'test',
            'type' => 1
        ]);

        $this->assertEquals(1, $castMember->type);

        $castMember = CastMember::create([
            'name' => 'test',
            'type' => '2'
        ]);

        $this->assertEquals(2, $castMember->type);
    }

    public function testUpdate()
    {
        $castMember = factory(CastMember::class)->create([
            'name' => 'test',
            'type' => 1,
        ])->first();

        $data = [
            'name' => 'test_name_updated',
            'type' => 2
        ];

        $castMember->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete()
    {
        /** @var CastMember $castMember */
        $castMember = factory(CastMember::class)->create()->first();

        $castMember->delete();

        $this->assertNotNull($castMember->deleted_at);
        $this->assertNotNull(CastMember::onlyTrashed()->first());
    }
}
