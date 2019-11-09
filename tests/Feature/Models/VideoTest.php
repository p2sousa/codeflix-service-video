<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Traits\TestUuid;

class VideoTest extends TestCase
{
    use DatabaseMigrations;
    use TestUuid;

    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_FREE,
            'duration' => 10,
        ];
    }

    public function testList()
    {
        factory(Video::class, 1)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);
        $videoKey = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'video_file',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            $videoKey
        );
    }

    public function testCreateWithBasicFields()
    {
        $video = Video::create($this->data);

        $video->refresh();

        $this->assertTrue($this->isUuid($video->id));
        $this->assertEquals('title', $video->title);
        $this->assertEquals('description', $video->description);
        $this->assertEquals(Video::RATING_FREE, $video->rating);
        $this->assertEquals(10, $video->duration);
        $this->assertEquals(2010, $video->year_launched);
        $this->assertFalse($video->opened);

        $video = Video::create($this->data + ['opened' => true]);

        $this->assertTrue($video->opened);
        $this->assertEquals('2010', $video->year_launched);
        $this->assertEquals(Video::RATING_FREE, $video->rating);
    }

    public function testCreateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ]);
        $video->refresh();

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testCreateWithUpload()
    {
        $file = UploadedFile::fake()
            ->create('video1.mp4')
            ->size(5000);

        $video = Video::create($this->data + [
                'video_file' => $file,
            ]);
        $video->refresh();

        $this->assertEquals($video->video_file, $file->hashName());
        \Storage::assertExists("{$video->id}/{$video->video_filename}");
    }

    public function testUpdateWithBasicFields()
    {
        $video = factory(Video::class)->create(['opened' => false]);
        $video->update($this->data);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => false]);

        $video = factory(Video::class)->create(['opened' => true]);
        $video->update($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }

    public function testUpdateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = factory(Video::class)->create();

        $video->update($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testDelete()
    {
        /** @var Video $video */
        $video = factory(Video::class)->create()->first();

        $video->delete();

        $this->assertNotNull($video->deleted_at);
        $this->assertNotNull(Video::onlyTrashed()->first());
    }

    public function testRollBackCreate()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_FREE,
                'duration' => 10,
                'categories_id' =>  [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollBackUpdate()
    {
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;
        $hasError = false;
        $data = $this->data + ['categories_id' =>  [0, 1, 2]];
        try {
            $video->update($data);
        } catch (QueryException $exception) {
            $this->assertDatabaseHas('videos', [
                'title' => $oldTitle
            ]);
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testHandleRelations()
    {
        $video = factory(Video::class)->create();
        Video::handleRelations($video, []);
        $this->assertCount(0, $video->categories);
        $this->assertCount(0, $video->genres);

        $category = factory(Category::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);

        $genre = factory(Genre::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->genres);

        $video->categories()->delete();
        $video->genres()->delete();

        Video::handleRelations($video, [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1, $video->categories);
        $this->assertCount(1, $video->genres);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)
            ->create()
            ->pluck('id')
            ->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[0]]
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ]);

        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);
    }

    public function testSyncGenres()
    {
        $genresId = factory(Genre::class, 3)
            ->create()
            ->pluck('id')
            ->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genresId[0]]
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'genres_id' => [$genresId[1], $genresId[2]]
        ]);

        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $video->id
        ]);
    }

    private function assertHasCategory($video_id, $cagegory_id)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $video_id,
            'category_id' => $cagegory_id
        ]);
    }

    private function assertHasGenre($video_id, $genre_id)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $video_id,
            'genre_id' => $genre_id
        ]);
    }
}
