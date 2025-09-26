@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-body">
      <form class="row g-2 mb-3" method="GET" action="{{ route('rooms.index') }}">
        <div class="col-sm-8 col-md-10">
          <input name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nombre...">
        </div>
        <div class="col-sm-4 col-md-2 d-grid">
          <button class="btn btn-primary">Buscar</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Capacidad</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($rooms as $room)
              <tr>
                <td>{{ $room->name }}</td>
                <td>{{ $room->capacity }}</td>
                <td>
                  @php
                    $map = [
                      'disponible' => 'success',
                      'ocupada' => 'danger',
                      'mantenimiento' => 'warning',
                    ];
                  @endphp
                  <span class="badge text-bg-{{ $map[$room->status] ?? 'secondary' }}">
                    {{ ucfirst($room->status) }}
                  </span>
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('rooms.edit', $room) }}">Editar</a>
                  <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar sala?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted">Sin resultados</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $rooms->links() }}
    </div>
  </div>
@endsection
