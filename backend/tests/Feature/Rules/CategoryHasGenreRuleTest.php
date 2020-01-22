<?php

namespace Tests\Feature\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Rules\CategoryHasGenreRule;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryHasGenreRuleTest extends TestCase
{
    use DatabaseMigrations;

    protected $categories;
    protected $genres;

    public function setUp(): void
    {
        parent::setUp();

        $this->categories = factory(Category::class, 5)->create();
        $this->genres = factory(Genre::class, 5)->create();

        $this->genres[0]->categories()->sync([
            $this->categories[0]->id,
            $this->categories[1]->id
        ]);

        $this->genres[1]->categories()->sync([
            $this->categories[2]->id,
        ]);
    }

    public function testIfPassesIsValid()
    {
        $rule = new CategoryHasGenreRule([
            $this->genres[0]->id
        ]);

        $isValid = $rule->passes('category_id',
            [
                $this->categories[0]->id
            ]
        );

        $this->assertTrue($isValid);

        $rule = new CategoryHasGenreRule([
            $this->genres[0]->id,
            $this->genres[1]->id
        ]);

        $isValid = $rule->passes('category_id',
            [
                $this->categories[0]->id,
                $this->categories[1]->id
            ]
        );

        $this->assertTrue($isValid);
    }

    public function testIfPassesIsNotValid()
    {
        $rule = new CategoryHasGenreRule([
            $this->genres[2]->id
        ]);

        $isValid = $rule->passes('category_id',
            [
                $this->categories[0]->id,
                $this->categories[1]->id
            ]
        );

        $this->assertFalse($isValid);
    }
}
