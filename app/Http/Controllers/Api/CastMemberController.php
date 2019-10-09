<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;

class CastMemberController extends BasicController
{
    private $rules;

    /**
     * CastMemberController constructor.
     */
    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',', CastMember::typeMembers())
        ];
    }

    protected function model()
    {
        return CastMember::class;
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
