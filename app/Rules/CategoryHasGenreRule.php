<?php

namespace App\Rules;

use App\Exceptions\CategoryNotRelationWithGenreException;
use Illuminate\Contracts\Validation\Rule;

class CategoryHasGenreRule implements Rule
{
    protected $categories;
    protected $genres;

    /**
     * Create a new rule instance.
     *
     * @param $genres
     */
    public function __construct($genres)
    {
        $this->genres = is_array($genres) ? array_unique($genres) : $genres;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            if (!is_array($value)) {
                throw new CategoryNotRelationWithGenreException();
            }

            $this->categories = array_unique($value);

            if (!count($this->genres) || !count($this->categories)) {
                throw new CategoryNotRelationWithGenreException();
            }

            $categoriesWithRelations = $this->categoriesRelations();

            if (count($categoriesWithRelations) != count($this->categories)) {
                throw new CategoryNotRelationWithGenreException();
            }

            return true;

        } catch (CategoryNotRelationWithGenreException $ex) {
            return false;
        }
    }

    protected function categoriesRelations()
    {
        $categoriesFound = [];
        foreach ( $this->categories as $categoryId) {
            $categoryGenres = $this->checkIfExistRelation($categoryId);
            $categoryGenres->pluck('id')->toArray();
            array_push(
                $categoriesFound,
                ...$categoryGenres->pluck('category_id')->toArray()
            );
        }

        $categoriesFound = array_unique($categoriesFound);
        return $categoriesFound;
    }

    protected function checkIfExistRelation($categoryId)
    {
        $categoryGenres = \DB::table('category_genre')
            ->where('category_id', $categoryId)
            ->whereIn('genre_id', $this->genres)
            ->get();

        if (!count($categoryGenres)) {
            throw new CategoryNotRelationWithGenreException();
        }

        return $categoryGenres;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A category must be related at least a genre.';
    }
}
