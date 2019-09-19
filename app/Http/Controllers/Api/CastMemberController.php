<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;

class CastMemberController extends BasicController
{
    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255',
            'type' => 'required|integer'
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'type' => 'required|integer'
        ];
    }

}
