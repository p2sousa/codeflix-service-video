<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genres = factory(Genre::class)->create();

        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genres->toArray()]);
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationRulePost()
    {
        $response = $this->json('POST', route('genres.store'), []);

        $this->assertInvalidationNameRequired($response);

        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);

        $this->assertInvalidationNameMax($response);
        $this->assertInvalidationIsActiveBoolean($response);
    }

    public function testInvalidationRulePut()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), []);

        $this->assertInvalidationNameRequired($response);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
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
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'teste'
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());

        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genres.store'), [
            'name' => 'teste',
            'is_active' => false,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'is_active' => false,
            ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'teste',
            'is_active' => true,
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'name' => 'teste',
                'is_active' => true,
            ]);
    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]));

        $response
            ->assertStatus(204);

        $this->assertEmpty($response->getContent());

    }
}
