<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();

        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description',
        ]);

        $result = $this->controller->index();
        $serialized = $result->response()->getData(true);

        $this->assertEquals([$category->toArray()], $serialized['data']);
        $this->assertArrayHasKey('meta', $serialized);
        $this->assertArrayHasKey('links', $serialized);
    }

    public function testShow()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test']);
        $category->refresh();

        $result = $this->controller->show($category->id);
        $serialized = $result->response()->getData(true);

        $this->assertEquals($category->toArray(), $serialized['data']);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);

        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn([
                'name' => 'test_name',
                'description' => 'test_description'
            ]);

        $result = $this->controller->store($request);
        $serialized = $result->response()->getData(true);

        $this->assertEquals(
            CategoryStub::first()->toArray(),
            $serialized['data']
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create([
            'name' => 'test_name',
            'description' => 'test_description',
        ]);

        $reflectionClass = new \ReflectionClass(BasicController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowExceptionWhenIdInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BasicController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testUpdate()
    {
        /** @var CategoryStub $category */
        $category =  CategoryStub::create(['name' => 'test']);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_update']);

        $result =  $this->controller->update($request, $category->id);
        $serialized = $result->response()->getData(true);

        $this->assertEquals(
            CategoryStub::find($category->id)->toArray(),
            $serialized['data']
        );
    }

    public function testDestroy()
    {
        /** @var CategoryStub $category */
        $category =  CategoryStub::create(['name' => 'test']);

        $obj = $this->controller->destroy($category->id);
        $this->assertEquals($obj->getStatusCode(), 204);
    }
}
