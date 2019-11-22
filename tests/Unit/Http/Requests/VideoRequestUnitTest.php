<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\VideoRequest;
use App\Models\Video;
use App\Rules\CategoryHasGenreRule;
use Tests\TestCase;

class VideoRequestUnitTest extends TestCase
{
    private $videoRequest;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var VideoRequest videoRequest */
        $this->videoRequest = new VideoRequest();
    }

    public function testAuthorizeMethod()
    {
        $this->assertTrue($this->videoRequest->authorize());
    }

    public function testRulesMethod()
    {
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::ratings()),
            'duration' => 'required|integer',
            'categories_id' => [
                'required',
                'array',
                'exists:categories,id,deleted_at,NULL',
                new CategoryHasGenreRule('')
            ],
            'genres_id' => 'required|array|exists:genres,id,deleted_at,NULL',
            'video_file' => 'filled|mimetypes:video/mp4|max:'. Video::VIDEO_FILE_MAX_SIZE,
            'trailer_file' => 'filled|mimetypes:video/mp4|max:'. Video::TRAILER_FILE_MAX_SIZE,
            'thumb_file' => 'filled|image|max:'. Video::THUMB_FILE_MAX_SIZE,
            'banner_file' => 'filled|image|max:'. Video::BANNER_FILE_MAX_SIZE,
        ];

        $this->assertEquals($rules, $this->videoRequest->rules());
    }
}
