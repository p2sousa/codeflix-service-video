<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    use Uuid;

    const RATING_FREE = 'L';
    const RATING_10 = '10';
    const RATING_12 = '12';
    const RATING_14 = '14';
    const RATING_16 = '16';
    const RATING_18 = '18';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer',
    ];

    public static function ratings(): array
    {
        return [
            self::RATING_FREE,
            self::RATING_10,
            self::RATING_12,
            self::RATING_14,
            self::RATING_16,
            self::RATING_18
        ];
    }

    public function categories()
    {
        return$this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return$this->belongsToMany(Genre::class)->withTrashed();
    }
}
