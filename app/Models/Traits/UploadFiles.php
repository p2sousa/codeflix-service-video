<?php

namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait UploadFiles
{
    public $oldFiles = [];

    public static abstract function fileFields(): array;
    protected abstract function uploadDirectory();

    public static function bootUploadFiles()
    {
        static::updating(function (Model $model) {
            $fieldsUpdated = array_keys($model->getDirty());
            $filesUpdated = array_intersect($fieldsUpdated, self::fileFields());
            $filesFiltered = Arr::where($filesUpdated, function($fileField) use($model) {
                return $model->getOriginal($fileField);
            });

            $model->oldFiles = array_map(function($fileField) use ($model) {
                return $model->getOriginal($fileField);
            }, $filesFiltered);

        });
    }

    public function relativeFilePath($value)
    {
        return "{$this->uploadDirectory()}/{$value}";
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    /**
     * @param UploadedFile $file
     */
    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDirectory());
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    /**
     * @param UploadedFile[] $files
     */
    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile($file)
    {
        $filename = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDirectory()}/{$filename}");
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::fileFields() as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }

        return $files;
    }

    public function getFileUrl($filename)
    {
        return \Storage::url($this->relativeFilePath($filename));
    }
}
