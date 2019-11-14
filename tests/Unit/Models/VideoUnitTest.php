<?php

namespace Tests\Unit\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class VideoUnitTest extends TestCase
{
    private $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = new Video();
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
            UploadFiles::class
        ];

        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testFillableProperty()
    {
        $fillable = [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'video_file',
            'thumb_file'
        ];
        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testDatesProperty()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->video->getDates());
        }

        $this->assertCount(count($dates), $this->video->getDates());
    }

    public function testCastsProperty()
    {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer',
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testIncrementingProperty()
    {
        $this->assertFalse($this->video->incrementing);
    }

    public function testFileFields()
    {
        $fileFields = ['video_file', 'thumb_file'];
        $this->assertEquals($fileFields, Video::fileFields());
    }

    public function testRatingsList()
    {
        $ratings = ['L', '10', '12', '14', '16', '18'];
        $this->assertEquals($ratings, Video::ratings());
    }

    public function testRatingsConstants()
    {
        $this->assertEquals('L', Video::RATING_FREE);
        $this->assertEquals('10', Video::RATING_10);
        $this->assertEquals('12', Video::RATING_12);
        $this->assertEquals('14', Video::RATING_14);
        $this->assertEquals('16', Video::RATING_16);
        $this->assertEquals('18', Video::RATING_18);
    }
}
