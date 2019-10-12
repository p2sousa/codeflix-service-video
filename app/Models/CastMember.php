<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes;
    use Uuid;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    public $incrementing = false;
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string',
        'type' => 'integer'
    ];

    public static function typeMembers(): array
    {
        return [
            self::TYPE_DIRECTOR,
            self::TYPE_ACTOR
        ];
    }

}
