<?php

namespace App\Http\Requests;

use App\Models\Artist;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateArtistRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('artist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'slug'  => [
                'string',
                'nullable',
            ],
            'name'  => [
                'string',
                'required',
            ],
            'phone' => [
                'string',
                'required',
                'unique:artists,phone,' . request()->route('artist')->id,
            ],
        ];
    }
}