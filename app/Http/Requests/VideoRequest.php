<?php

namespace App\Http\Requests;

use App\Models\Video;
use App\Rules\CategoryHasGenreRule;
use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::ratings()),
            'duration' => 'required|integer',
            'categories_id' => [
                'required',
                'array',
                'exists:categories,id,deleted_at,NULL',
                new CategoryHasGenreRule($this->request->get('genres_id'))
            ],
            'genres_id' => 'required|array|exists:genres,id,deleted_at,NULL',
            'video_file' => 'filled|mimetypes:video/mp4|max:100000',
            'thumb_file' => 'filled|mimetypes:image/jpeg,image/png|max:10000'
        ];
    }
}
