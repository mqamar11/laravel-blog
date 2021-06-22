@extends('layouts.app')



@section('content')

<div class="panel panel-default">
    <div class="">
       <h4>Tags List</h4>
    </div>
    <div class="panel-body">
        <table class="table table-hover">
            <thead>
                <th>Se.</th>
                <th>Name</th>
                <th>Edit</th>
                <th>Delete</th>

            </thead>

            <tbody>

                @if ($tags->count() > 0)


                @foreach ($tags as $tag)
                <tr>
                    <td>
                        {{$loop->iteration}}
                    </td>
                    <td>{{$tag->tag}}</td>

                    <td>
                        <a href="{{ route('tag.edit', ['id' => $tag->id ]) }}" class="btn btn-xs btn-info">Edit</a>
                    </td>

                    <td>
                        <a href="{{ route('tag.delete',['id' => $tag->id ]) }}" class="btn btn-xs btn-danger">Delete</a>

                    </td>
                </tr>
                @endforeach

                @else
                <tr>
                    <th colspan="5" class="text-center">No tags yet </th>
                </tr>

                @endif
            </tbody>
        </table>
    </div>
</div>



@endsection
