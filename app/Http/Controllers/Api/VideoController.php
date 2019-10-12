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

        $self = $this;
        $video = \DB::transaction(function() use($request, $validateData, $self) {
            $video = Video::create($validateData);
            $self->handleRelations($video, $request);
            return $video;
        });

        $video->refresh();
        return $video;
    }

    public function update(VideoRequest $request, Video $video)
    {
        $validation = $request->validated();

        $self = $this;
        $video = \DB::transaction(function() use($request, $validation, $self, $video) {
            $video->update($validation);
            $self->handleRelations($video, $request);
            return $video;
        });

        $video->refresh();
        return $video;
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return response()->noContent();
    }

    protected function handleRelations($video, VideoRequest $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('genres_id'));
    }
}
