@extends('layouts.app')

@section('content')
<h3>Edit {{ ucfirst($table) }}</h3>

<form action="{{ route($table . '.update', [$item->id]) }}" method="POST">
    @csrf
    @method('PUT')
    @foreach($columns as $col)
        @if(in_array($col, ['id','created_at','updated_at']))
            @continue
        @endif
        
        <div class="mb-3">
            <label class="form-label">{{ ucfirst(str_replace('_', ' ', $col)) }}</label>
            
            @if(str_contains($col, 'email'))
                <input type="email" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" placeholder="email@example.com" />
            @elseif(str_contains($col, 'password'))
                <input type="password" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" />
            @elseif(str_contains($col, 'phone') || str_contains($col, 'mobile'))
                <input type="tel" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" placeholder="+44 7XXX XXXXXX" />
            @elseif(str_contains($col, 'date') || str_contains($col, '_at'))
                <input type="date" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" />
            @elseif(str_contains($col, 'price') || str_contains($col, 'amount') || str_contains($col, 'cost') || str_contains($col, 'limit') || str_contains($col, 'balance'))
                <input type="number" step="0.01" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" placeholder="0.00" />
            @elseif(str_contains($col, 'address') || str_contains($col, 'description') || str_contains($col, 'notes'))
                <textarea name="{{ $col }}" class="form-control" rows="3">{{ $item->{$col} ?? '' }}</textarea>
            @else
                <input type="text" name="{{ $col }}" class="form-control" value="{{ $item->{$col} ?? '' }}" />
            @endif
        </div>
    @endforeach
    <button class="btn btn-primary">Update</button>
    <a href="{{ route($table . '.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
