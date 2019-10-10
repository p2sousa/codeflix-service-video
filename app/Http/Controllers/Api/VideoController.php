<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;

class VideoController extends BasicController
{
    private $rules;

    /**
     * VideoController constructor.
     */
    public function __construct()
    {
        $this->rules = [];
    }

    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
