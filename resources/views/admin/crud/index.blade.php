@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>{{ ucfirst($table) }}</h3>
    <a href="{{ route($table . '.create') }}" class="btn btn-primary">Create New</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-striped">
    <thead>
        <tr>
            @foreach($columns as $col)
                @if(in_array($col, ['created_at','updated_at']))
                    @continue
                @endif
                <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
            @endforeach
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                @foreach($columns as $col)
                    @if(in_array($col, ['created_at','updated_at']))
                        @continue
                    @endif
                    <td>{{ is_object($item) ? ($item->{$col} ?? 'N/A') : (array_key_exists($col, (array)$item) ? $item[$col] : 'N/A') }}</td>
                @endforeach
                <td>
                    <a href="{{ route($table . '.show', [$item->id]) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    <a href="{{ route($table . '.edit', [$item->id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route($table . '.destroy', [$item->id]) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $items->links() }}
@endsection
