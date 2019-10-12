<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CastMemberUnitTest extends TestCase
{
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = new CastMember();
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];

        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $castMemberTraits);
    }

    public function testFillableProperty()
    {
        $fillable = ['name', 'type'];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }

    public function testDatesProperty()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->castMember->getDates());
        }

        $this->assertCount(count($dates), $this->castMember->getDates());
    }

    public function testCastsProperty()
    {
        $casts = [
            'id' => 'string',
            'type' => 'integer'
        ];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testIncrementingProperty()
    {
        $this->assertFalse($this->castMember->incrementing);
    }

    public function testTypeMembers()
    {
        $typeMembers = [1, 2];
        $this->assertEquals($typeMembers, CastMember::typeMembers());
    }

    public function testTypeMembersConstants()
    {
        $this->assertEquals(1, CastMember::TYPE_DIRECTOR);
        $this->assertEquals(2, CastMember::TYPE_ACTOR);
    }
}
