{{-- resources/views/rooms/edit.blade.php --}}
@extends('layouts.app')

@section('sidebar')
  @include('rooms._sidebar_min')
@endsection

@section('content')
  <div class="card" style="max-width:640px">
    <div class="card-header">Editar sala</div>
    <div class="card-body">
      <form method="POST" action="{{ route('rooms.update', $room) }}">
        @csrf
        @method('PUT') {{-- ¡IMPORTANTE! --}}
        @include('rooms._form', ['room' => $room])

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Actualizar</button>
          <a class="btn btn-outline-secondary" href="{{ route('rooms.index') }}">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
@endsection
