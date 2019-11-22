<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Support\Arr;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations;
    use TestSaves;
    use TestResources;

    private $serializedFields = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'video_file',
        'trailer_file',
        'thumb_file',
        'banner_file',
        'thumb_file_url',
        'banner_file_url',
        'video_file_url',
        'trailer_file_url',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        ]
    ];

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => [],
            ]);

        $resource = VideoResource::collection(collect([$this->video]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->serializedFields
            ]);
        $id = $response->json('data.id');
        $resource = new VideoResource(Video::find($id));
        $this->assertResource($response, $resource);
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
        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + ['opened' => true],
                'test_data' => $testData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + ['rating' => Video::RATING_18],
                'test_data' => $testData + ['rating' => Video::RATING_18]
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

            $id = $response->json('data.id');
            $resource = new VideoResource(Video::find($id));
            $this->assertResource($response, $resource);

            $this->assertIfHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('data.id'),
                $value['send_data']['genres_id'][0]
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                'data' => $this->serializedFields
            ]);

            $id = $response->json('data.id');
            $resource = new VideoResource(Video::find($id));
            $this->assertResource($response, $resource);

            $this->assertIfHasCategory(
                $response->json('data.id'),
                $value['send_data']['categories_id'][0]
            );

            $this->assertIfHasGenre(
                $response->json('data.id'),
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
}
