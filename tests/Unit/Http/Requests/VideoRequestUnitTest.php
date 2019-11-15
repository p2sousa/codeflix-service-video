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
            'video_file' => 'filled|mimetypes:video/mp4|max:50000000',
            'trailer_file' => 'filled|mimetypes:video/mp4|max:1000000',
            'thumb_file' => 'filled|mimetypes:image/jpeg,image/png|max:5000',
            'banner_file' => 'filled|mimetypes:image/jpeg,image/png|max:10000',
        ];

        $this->assertEquals($rules, $this->videoRequest->rules());
    }
}
