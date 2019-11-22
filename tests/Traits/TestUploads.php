<?php

namespace Tests\Traits;

use App\Models\Traits\UploadFiles;
use Illuminate\Http\UploadedFile;

trait TestUploads
{
    use TestValidations;

    public function assertInvalidationFile($field, $extesion, $maxSize, $rule, $ruleParams = [])
    {
        $routes = [
            [
                'method' => 'POST',
                'route' => $this->routeStore()
            ],
            [
                'method' => 'PUT',
                'route' => $this->routeUpdate()
            ]
        ];
        foreach ($routes as $route){
            $file = UploadedFile::fake()->create("$field.1$extesion" );
            $response = $this->json($route['method'], $route['route'], [
                $field => $file
            ]);
            $this->assertInvalidationFields($response, [$field], $rule, $ruleParams);
            $file = UploadedFile::fake()->create("$field.$extesion" )->size($maxSize + 1);
            $response = $this->json($route['method'], $route['route'], [
                $field => $file
            ]);
            $this->assertInvalidationFields($response, [$field], 'max.file', ['max' => $maxSize]);
        }
    }

    protected function assertFilesExistsInStorage($model, array $files)
    {
        /** @var UploadFiles $file */
        foreach ($files as $file) {
            \Storage::assertExists($model->relativeFilePath($file->hashName()));
        }
    }
}
