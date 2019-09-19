<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicController;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BasicController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255'
        ];
    }


}
