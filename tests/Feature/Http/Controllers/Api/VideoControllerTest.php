<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestValidations;
    use TestSaves;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = factory(Video::class)->create();
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_FREE,
            'duration' => 10
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJsonFragment($this->video->toArray());
    }

    public function testInvalidationRequiredRule()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
        ];

        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');
    }

    public function testInvalidationMaxRule()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];

        $this->assertInvalidationInStoreAction($data,'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data,'max.string', ['max' => 255]);
    }

    public function testInvalidationIntegerRule()
    {
        $data = [
            'duration' => 'time'
        ];

        $this->assertInvalidationInStoreAction($data,'integer');
        $this->assertInvalidationInUpdateAction($data,'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data,'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data,'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 'a'
        ];

        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];

        $this->assertInvalidationInStoreAction($data,'in');
        $this->assertInvalidationInUpdateAction($data,'in');
    }

    public function testStore()
    {
        $response = $this->assertStore(
            $this->sendData,
            $this->sendData + ['opened' => false]
        );

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertStore(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]
        );

        $this->assertStore(
            $this->sendData + ['rating' => Video::RATING_18],
            $this->sendData + ['rating' => Video::RATING_18]
        );
    }

    public function testUpdate()
    {
        $response = $this->assertUpdate(
            $this->sendData,
            $this->sendData + ['opened' => false]
        );

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertUpdate(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]
        );

        $this->assertUpdate(
            $this->sendData + ['rating' => Video::RATING_18],
            $this->sendData + ['rating' => Video::RATING_18]
        );
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));

        $response
            ->assertStatus(204);

        $this->assertEmpty($response->getContent());
    }

    protected function model()
    {
        return Video::class;
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }
}