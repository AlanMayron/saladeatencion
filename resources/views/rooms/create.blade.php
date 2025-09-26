@extends('layouts.app')

@section('sidebar')
  @include('rooms._sidebar_min')
@endsection

@section('content')
  <div class="card" style="max-width: 640px">
    <div class="card-header">Nueva sala</div>
    <div class="card-body">
      {{-- POST a rooms.store --}}
      <form method="POST" action="{{ route('rooms.store') }}">
        @csrf
        @include('rooms._form')
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-success">Crear</button>
          <a class="btn btn-outline-secondary" href="{{ route('rooms.index') }}">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
@endsection
