<?php

namespace Tests\Stubs\Models\Traits;

use App\Models\Traits\UploadFiles;
use Illuminate\Database\Eloquent\Model;


class UploadFilesStub extends Model
{
    use UploadFiles;

    public static function fileFields(): array
    {
        return ['file1', 'file2'];
    }

    protected function uploadDirectory()
    {
        return "1";
    }

}