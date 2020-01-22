<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BasicController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
        ];
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());

        $self = $this;
        $genre = \DB::transaction(function() use($request, $validateData, $self) {
            $genre = $this->model()::create($validateData);
            $self->handleRelations($genre, $request);
            return $genre;
        });

        $genre->refresh();
        $resource = $this->resource();
        return new $resource($genre);
    }

    public function update(Request $request, $id)
    {
        $validation = $this->validate($request, $this->rulesUpdate());
        $genre = $this->findOrFail($id);

        $self = $this;
        $genre = \DB::transaction(function() use($request, $validation, $self, $genre) {
            $genre->update($validation);
            $self->handleRelations($genre, $request);
            return $genre;
        });

        $genre->refresh();
        $resource = $this->resource();
        return new $resource($genre);
    }

    protected function handleRelations($genre, Request $request)
    {
        $genre->categories()->sync($request->get('categories_id'));
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return GenreResource::class;
    }
}
