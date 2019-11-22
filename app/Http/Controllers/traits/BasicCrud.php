<?php

namespace App\Http\Controllers\traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

trait BasicCrud
{
    protected $paginationSize = 15;

    protected abstract function model();
    protected abstract function resource();
    protected abstract function resourceCollection();

    public function index()
    {
        $data = !$this->paginationSize
            ? $this->model()::all()
            : $this->model()::paginate($this->paginationSize);


        $resourceCollectionClass = $this->resourceCollection();
        $refClass = new \ReflectionClass($this->resourceCollection());

        return $refClass->isSubclassOf(ResourceCollection::class)
            ? new $resourceCollectionClass($data)
            : $resourceCollectionClass::collection($data);
    }

    public function show($id)
    {
        $obj = $this->findOrFail($id);

        $resource = $this->resource();
        return new $resource($obj);
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName =  (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }
}
