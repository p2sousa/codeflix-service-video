<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestUuid;

class VideoTest extends TestCase
{
    use DatabaseMigrations;
    use TestUuid;

    public function testList()
    {
        factory(Video::class, 1)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videoKey = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            $videoKey
        );
    }

    public function testCreate()
    {
        $video = Video::create([
            'title' => 'test',
            'description' => 'test description',
            'year_launched' => 2019,
            'rating' => Video::RATING_FREE,
            'duration' => 20,
        ]);

        $video->refresh();

        $this->assertTrue($this->isUuid($video->id));
        $this->assertEquals('test', $video->title);
        $this->assertEquals('test description', $video->description);
        $this->assertEquals(Video::RATING_FREE, $video->rating);
        $this->assertEquals(20, $video->duration);
        $this->assertEquals(2019, $video->year_launched);
        $this->assertFalse($video->opened);

        $video = Video::create([
            'title' => 'test',
            'description' => 'test description',
            'year_launched' => '2019',
            'rating' => Video::RATING_18,
            'duration' => 12,
            'opened' => true
        ]);

        $this->assertTrue($video->opened);
        $this->assertEquals('2019', $video->year_launched);
        $this->assertEquals(Video::RATING_18, $video->rating);
    }

    public function testUpdate()
    {
        $video = factory(Video::class)->create([
            'title' => 'test',
            'rating' => Video::RATING_FREE,
        ])->first();

        $data = [
            'title' => 'test update',
            'description' => 'update',
            'year_launched' => '2020',
            'rating' => Video::RATING_18,
            'duration' => 12,
            'opened' => true
        ];

        $video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Video $video */
        $video = factory(Video::class)->create()->first();

        $video->delete();

        $this->assertNotNull($video->deleted_at);
        $this->assertNotNull(Video::onlyTrashed()->first());
    }

    public function testRollBackCreate()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_FREE,
                'duration' => 10,
                'categories_id' =>  [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollBackUpdate()
    {
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;
        $hasError = false;
        try {
            $video->update([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_FREE,
                'duration' => 10,
                'categories_id' =>  [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}
