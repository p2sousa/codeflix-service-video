<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;

class VideoUploadTest extends BaseVideoTest
{
    public function testCreateWithFile()
    {
        \Storage::fake();
        $file1 = UploadedFile::fake()->image('thumb.jpg');
        $file2 = UploadedFile::fake()->create('video1.mp4');

        $video = Video::create(
            $this->data + [
                'thumb_file' => $file1,
                'video_file' => $file2,
            ]
        );
        $video->refresh();

        $this->assertEquals($video->thumb_file, $file1->hashName());
        $this->assertEquals($video->video_file, $file2->hashName());

        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });

        $hasError = false;

        try {
            Video::create(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->create('video1.mp4'),
                ]
            );

        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testUpdateWithFile()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        $file1 = UploadedFile::fake()->image('thumb.jpg');
        $file2 = UploadedFile::fake()->create('video1.mp4');

        $video->update(
            $this->data + [
                'thumb_file' => $file1,
                'video_file' => $file2,
            ]
        );
        $video->refresh();

        $this->assertEquals($video->thumb_file, $file1->hashName());
        $this->assertEquals($video->video_file, $file2->hashName());

        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");

        $newVideoFile = UploadedFile::fake()->image('video.mp4');

        $video->update(
            $this->data + [
                'video_file' => $newVideoFile,
            ]
        );
        \Storage::assertExists("{$video->id}/{$file1->hashName()}");
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertMissing("{$video->id}/{$file2->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            $video->update(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->create('video1.mp4'),
                ]
            );

        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}
