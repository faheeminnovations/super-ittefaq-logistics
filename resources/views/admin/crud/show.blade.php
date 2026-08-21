@extends('layouts.app')

@section('content')
<h3>{{ ucfirst($table) }} Details</h3>

<table class="table">
    <tbody>
        @foreach($columns as $col)
            @if(in_array($col, ['id','created_at','updated_at']))
                @continue
            @endif
            <tr>
                <th>{{ ucfirst(str_replace('_', ' ', $col)) }}</th>
                <td>{{ $item->{$col} ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route($table . '.index') }}" class="btn btn-secondary">Back</a>
@endsection
