@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.artist.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.artists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.artist.fields.id') }}
                        </th>
                        <td>
                            {{ $artist->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.artist.fields.slug') }}
                        </th>
                        <td>
                            {{ $artist->slug }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.artist.fields.name') }}
                        </th>
                        <td>
                            {{ $artist->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.artist.fields.phone') }}
                        </th>
                        <td>
                            {{ $artist->phone }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.artist.fields.image') }}
                        </th>
                        <td>
                            @if($artist->image)
                                <a href="{{ $artist->image->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $artist->image->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.artists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection