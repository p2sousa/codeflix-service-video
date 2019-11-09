<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
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

        $this->video = factory(Video::class)->create(['opened' => false]);

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_FREE,
            'duration' => 10,
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
            'categories_id' => '',
            'genres_id' => '',
        ];

        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');
    }

    public function testInvalidationMaxRule()
    {
        $data = [
            'title' => str_repeat('a', 256),
        ];

        $this->assertInvalidationInStoreAction($data,'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data,'max.string', ['max' => 255]);
    }

    public function testInvalidationFilledRule()
    {
        $data = [
            'video_file' => ''
        ];

        $this->assertInvalidationInStoreAction($data,'filled');
    }

    public function testInvalidationMaxFileSizeRule()
    {
        $file = UploadedFile::fake()
            ->create('video1.mp4')
            ->size(100001);

        $data = [
            'video_file' => $file
        ];

        $this->assertInvalidationInStoreAction($data,'max.file', ['max' => 100000]);
    }

    public function testInvalidationFileMimeTypeRule()
    {
        $file = UploadedFile::fake()
            ->create('video1.pdf');

        $data = [
            'video_file' => $file
        ];

        $this->assertInvalidationInStoreAction($data,'mimetypes', ['values' => 'video/mp4']);
    }

    public function testInvalidationIntegerRule()
    {
        $data = [
            'duration' => 'time'
        ];

        $this->assertInvalidationInStoreAction($data,'integer');
        $this->assertInvalidationInUpdateAction($data,'integer');
    }

    public function testInvalidationArrayRule()
    {
        $data = [
            'categories_id' => 'a',
            'genres_id' => 'a',
        ];

        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');
    }

    public function testInvalidationExistsRule()
    {
        $data = [
            'categories_id' => [100],
            'genres_id' => [100],
        ];

        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');
    }

    public function testInvalidationExistsDeletedRule()
    {
        $category = factory(Category::class)->create();
        $category->delete();

        $genre = factory(Genre::class)->create();
        $genre->delete();

        $data = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ];

        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');
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

    public function testSaveWithoutFiles()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync([$category->id]);

        $relations = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];

        $data = [
            [
                'send_data' => $this->sendData + $relations,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + ['opened' => true] + $relations,
                'test_data' => $this->sendData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + ['rating' => Video::RATING_18] + $relations,
                'test_data' => $this->sendData + ['rating' => Video::RATING_18]
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);

            $this->assertIfHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);

            $this->assertIfHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );
        }
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]));

        $response
            ->assertStatus(204);

        $this->assertEmpty($response->getContent());
    }

    protected function assertIfHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video',
            [
                'category_id' => $categoryId,
                'video_id' => $videoId
            ]
        );
    }

    protected function assertIfHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video',
            [
                'genre_id' => $genreId,
                'video_id' => $videoId
            ]
        );
    }

    public function testStoreWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync([$category->id]);

        $relations = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData + $relations + $files
        );

        $response->assertStatus(201);
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("{$id}/{$file->hashName()}");
        }
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync([$category->id]);

        $relations = [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData + $relations + $files
        );

        $response->assertStatus(200);
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("{$id}/{$file->hashName()}");
        }
    }

    protected function getFiles()
    {
        $videofile = UploadedFile::fake()
            ->create('video1.mp4');

        return [
            'video_file' => $videofile
        ];
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
