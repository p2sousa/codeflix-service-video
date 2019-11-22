<?php

namespace App\Http\Controllers\traits;

use Illuminate\Http\Request;

trait AdvancedCrud
{
    use BasicCrud;

    protected abstract function rulesStore();
    protected abstract function rulesUpdate();

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validateData);
        $obj->refresh();

        $resource = $this->resource();
        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {
        $validation = $this->validate($request, $this->rulesUpdate());
        $obj = $this->findOrFail($id);
        $obj->update($validation);
        $obj->refresh();

        $resource = $this->resource();
        return new $resource($obj);
    }
}
