<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoRequest;
use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        return Video::all();
    }

    public function show(Video $video)
    {
        return $video;
    }

    public function store(VideoRequest $request)
    {
        $validateData = $request->validated();
        $video = Video::create($validateData);
        $video->refresh();
        return $video;
    }

    public function update(VideoRequest $request, Video $video)
    {
        $validation = $request->validated();
        $video->update($validation);
        $video->refresh();
        return $video;
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return response()->noContent();
    }
}
