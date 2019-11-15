<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Http\UploadedFile;
use Tests\Traits\TestUploads;

class VideoControllerUploadTest extends BaseVideoControllerTest
{
    use TestUploads;

    public function testInvalidationFilledRule()
    {
        $data = [
            'video_file' => '',
            'trailer_file' => '',
            'thumb_file' => '',
            'banner_file' => ''
        ];

        $this->assertInvalidationInStoreAction($data,'filled');
    }

    public function testInvalidationMaxFileSizeRule()
    {
        $this->assertInvalidationInStoreAction(
            ['video_file' => UploadedFile::fake()->create('video1.mp4')->size(50000001)],
            'max.file',
            ['max' => 50000000]
        );

        $this->assertInvalidationInStoreAction(
            ['trailer_file' => UploadedFile::fake()->create('trailler.mp4')->size(1000001)],
            'max.file',
            ['max' => 1000000]
        );

        $this->assertInvalidationInStoreAction(
            ['thumb_file' => UploadedFile::fake()->image('thumb.png')->size(5001)],
            'max.file',
            ['max' => 5000]
        );

        $this->assertInvalidationInStoreAction(
            ['banner_file' => UploadedFile::fake()->image('banner.png')->size(10001)],
            'max.file',
            ['max' => 10000]
        );
    }

    public function testInvalidationFileMimeTypeRule()
    {
        $videos = [
            'video_file' => UploadedFile::fake()->create('video1.pdf'),
            'trailer_file' => UploadedFile::fake()->create('trailler.pdf')
        ];

        $this->assertInvalidationInStoreAction($videos,'mimetypes', ['values' => 'video/mp4']);

        $images = [
            'thumb_file' => UploadedFile::fake()->create('thumb.mp4'),
            'banner_file' => UploadedFile::fake()->create('banner.mp4')
        ];

        $this->assertInvalidationInStoreAction($images,'mimetypes', ['values' => 'image/jpeg, image/png']);
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
        return [
            'video_file' => UploadedFile::fake()->create('video1.mp4'),
            'trailer_file' => UploadedFile::fake()->create('trailer_file.mp4'),
            'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
            'banner_file' => UploadedFile::fake()->image('banner.jpg'),
        ];
    }
}
