<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicController;
use Tests\Stubs\Models\CategoryStub;
use Tests\Stubs\Resources\CategoryResourceStub;

class CategoryControllerStub extends BasicController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }

    protected function resource()
    {
        return CategoryResourceStub::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }
}
