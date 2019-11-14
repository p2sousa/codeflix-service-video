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
            'thumb_file' => '',
        ];

        $this->assertInvalidationInStoreAction($data,'filled');
    }

    public function testInvalidationMaxFileSizeRule()
    {
        $fileVideo = UploadedFile::fake()
            ->create('video1.mp4')
            ->size(100001);

        $videos = [
            'video_file' => $fileVideo
        ];
        $this->assertInvalidationInStoreAction($videos,'max.file', ['max' => 100000]);

        $fileImage = UploadedFile::fake()
            ->image('thumb.png')
            ->size(10001);

        $images = [
            'thumb_file' => $fileImage
        ];
        $this->assertInvalidationInStoreAction($images,'max.file', ['max' => 10000]);
    }

    public function testInvalidationFileMimeTypeRule()
    {
        $file = UploadedFile::fake()
            ->create('video1.pdf');

        $data = [
            'video_file' => $file
        ];

        $this->assertInvalidationInStoreAction($data,'mimetypes', ['values' => 'video/mp4']);

        $fileImage = UploadedFile::fake()
            ->create('thumb.mp4');

        $images = [
            'thumb_file' => $fileImage
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
        $videofile = UploadedFile::fake()
            ->create('video1.mp4');

        $thumbfile = UploadedFile::fake()
            ->image('thumb.jpg');

        return [
            'video_file' => $videofile,
            'thumb_file' => $thumbfile,
        ];
    }
}
