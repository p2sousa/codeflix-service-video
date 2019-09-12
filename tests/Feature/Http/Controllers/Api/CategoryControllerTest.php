<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $categories = factory(Category::class)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$categories->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationRulePost()
    {
        $response = $this->json('POST', route('categories.store'), []);

        $this->assertInvalidationNameRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationNameMax($response);
        $this->assertInvalidationIsActiveBoolean($response);
    }

    public function testInvalidationRulePut()
    {
        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);

        $this->assertInvalidationNameRequired($response);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationNameMax($response);
        $this->assertInvalidationIsActiveBoolean($response);
    }

    protected function assertInvalidationNameRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::trans('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidationNameMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    protected function assertInvalidationIsActiveBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::trans('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'teste'
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));


        $response = $this->json('POST', route('categories.store'), [
            'name' => 'teste',
            'description' => 'description',
            'is_active' => false,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'description' => 'description',
                'is_active' => false,
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false,
        ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'teste',
            'description' => 'description test update',
            'is_active' => true,
        ]);

        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'name' => 'teste',
                'description' => 'description test update',
                'is_active' => true,
            ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'teste',
            'description' => '',
            'is_active' => true,
        ]);

        $response
            ->assertJsonFragment([
                'description' => null,
            ]);
    }
}
