<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;
    use Uuid;
    use UploadFiles;

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
        'video_file',
        'thumb_file'
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

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            /** @var Video $obj */
            $obj = static::query()->create($attributes);
            static::handleRelations($obj, $attributes);
            $obj->uploadFiles($files);
            \DB::commit();

            return $obj;
        } catch (\Exception $e) {
            if (isset($obj)) {
                $obj->deleteFiles($files);
            }
            \DB::rollBack();
            throw $e;
        }
    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);
        try {
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($saved) {
                $this->uploadFiles($files);
            }
            \DB::commit();

            if ($saved && count($files)) {
                $this->deleteOldFiles();
            }

            return $saved;
        } catch (\Exception $e) {
            $this->deleteFiles($files);
            \DB::rollBack();
            throw $e;
        }
    }

    public static function handleRelations(Video $video, array $attributes)
    {
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }

        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories()
    {
        return$this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return$this->belongsToMany(Genre::class)->withTrashed();
    }

    public static function fileFields(): array
    {
        return ['video_file', 'thumb_file'];
    }

    protected function uploadDirectory()
    {
        return $this->id;
    }
}
