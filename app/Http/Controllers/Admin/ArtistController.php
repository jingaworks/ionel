<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyArtistRequest;
use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ArtistController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('artist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artists = Artist::all();

        return view('admin.artists.index', compact('artists'));
    }

    public function create()
    {
        abort_if(Gate::denies('artist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.artists.create');
    }

    public function store(StoreArtistRequest $request)
    {
        $artist = Artist::create($request->all());

        if ($request->input('image', false)) {
            $artist->addMedia(storage_path('tmp/uploads/' . $request->input('image')))->toMediaCollection('image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $artist->id]);
        }

        return redirect()->route('admin.artists.index');
    }

    public function edit(Artist $artist)
    {
        abort_if(Gate::denies('artist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->load('created_by');

        return view('admin.artists.edit', compact('artist'));
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

        return redirect()->route('admin.artists.index');
    }

    public function show(Artist $artist)
    {
        abort_if(Gate::denies('artist_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->load('created_by');

        return view('admin.artists.show', compact('artist'));
    }

    public function destroy(Artist $artist)
    {
        abort_if(Gate::denies('artist_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $artist->delete();

        return back();
    }

    public function massDestroy(MassDestroyArtistRequest $request)
    {
        Artist::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('artist_create') && Gate::denies('artist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Artist();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}