<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\traits\BasicCrud;
use App\Http\Requests\VideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class VideoController extends Controller
{
    use BasicCrud;

    protected function model()
    {
        return Video::class;
    }

    public function store(VideoRequest $request)
    {
        $validateData = $request->validated();
        $video = Video::create($validateData);
        $video->refresh();
        $resource = $this->resource();
        return new $resource($video);
    }

    public function update(VideoRequest $request, Video $video)
    {
        $validation = $request->validated();
        $video->update($validation);
        $video->refresh();
        $resource = $this->resource();
        return new $resource($video);
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return VideoResource::class;
    }
}
