<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Http\Resources\Admin\ArtistResource;
use App\Models\Artist;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArtistApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('artist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ArtistResource(Artist::with(['created_by'])->get());
    }

    public function store(StoreArtistRequest $request)
    {
        $artist = Artist::create($request->all());

        if ($request->input('image', false)) {
            $artist->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        return (new ArtistResource($artist))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Artist $artist)
    {
        abort_if(Gate::denies('artist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ArtistResource($artist->load(['created_by']));
    }

    public function update(UpdateArtistRequest $request, Artist $artist)
    {
        $artist->update($request->all());

        if ($request->input('image', false)) {
            if (!$artist->image || $request->input('image') !== $artist->image->file_name) {
                if ($artist->image) {
                    $artist->image->delete();
                }

                $artist->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
            }
        } elseif ($artist->image) {
            $artist->image->delete();
        }

        return (new ArtistResource($artist))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Artist $artist)
    {
        abort_if(Gate::denies('artist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}