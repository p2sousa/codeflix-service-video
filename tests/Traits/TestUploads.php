<?php

namespace Tests\Traits;

use Illuminate\Http\UploadedFile;

trait TestUploads
{
    use TestValidations;

    public function assertInvalidationFile($field, $extesion, $maxSize, $rule, $ruleParans)
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
            $this->assertValidationFields($response, [$field], $rule, $ruleParans);
            $file = UploadedFile::fake()->create("$field.$extesion" )->size($maxSize + 1);
            $response = $this->json($route['method'], $route['route'], [
                $field => $file
            ]);
            $this->assertValidationFields($response, [$field], 'max.file', ['max' => $maxSize]);
        }
    }
}
