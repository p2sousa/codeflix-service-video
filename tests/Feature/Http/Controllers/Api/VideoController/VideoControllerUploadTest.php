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
}
