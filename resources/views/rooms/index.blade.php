@extends('layouts.app')

@section('sidebar')
  <form method="GET" action="{{ route('rooms.index') }}" class="mb-3">
    <div class="mb-2">
      <label class="form-label">Buscar por nombre</label>
      <input name="q" value="{{ $q }}" class="form-control" placeholder="Ej: Sala A-101">
    </div>

    <div class="mb-2">
      <label class="form-label">Resultados por página</label>
      <select name="perPage" class="form-select">
        @foreach([6,9,12,16,24,36] as $n)
          <option value="{{ $n }}" @selected(($rooms->perPage() ?? 12) == $n)>{{ $n }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-2">
  <label class="form-label">Ubicación</label>
  <select name="location" class="form-select">
    <option value="">— Todas —</option>

    @forelse($locations as $loc)
      <option value="{{ $loc }}" @selected(($location ?? '') === $loc)>{{ $loc }}</option>
    @empty
      <option disabled>(sin ubicaciones)</option>
    @endforelse
  </select>
</div>

    <button class="btn btn-primary w-100">Aplicar filtros</button>
  </form>
  <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('rooms.index') }}">Limpiar</a>
  </form>

  @if ($rooms->lastPage() > 1)
    <form method="GET" action="{{ route('rooms.index') }}" class="mb-3">
      <input type="hidden" name="q" value="{{ $q }}">
      <input type="hidden" name="perPage" value="{{ $rooms->perPage() }}">
      <label class="form-label">Ir a página</label>
      <div class="input-group">
        <input type="number" min="1" max="{{ $rooms->lastPage() }}" name="page"
               value="{{ $rooms->currentPage() }}" class="form-control">
        <button class="btn btn-outline-secondary">Ir</button>
      </div>
      <div class="form-text">Total: {{ $rooms->lastPage() }} páginas</div>
    </form>
  @endif

  <a href="{{ route('rooms.create') }}" class="btn btn-success w-100">
    <i class="fa-solid fa-plus me-1"></i> Nueva sala
  </a>
@endsection

@section('content')
<style>
  .room-card { width: 180px; overflow: hidden; } /* alto auto */
  .room-card .card-body { padding:.6rem; display:flex; flex-direction:column; gap:.45rem; }

  .room-head  { display:flex; align-items:flex-start; justify-content:space-between; gap:.4rem; }
  .room-title { min-width:0; }  /* permite truncar */
  .room-name  { font-weight:600; }
  .room-line  { font-size:.8rem; color:#6c757d; }

  .badge-tight { font-size:.72rem; line-height:1; padding:.25rem .35rem; white-space:nowrap; flex-shrink:0; }

  .person-icons { display:flex; flex-wrap:wrap; gap:.2rem; }
  .person-icon  { font-size:.95rem; }
</style>



  <div class="d-flex align-items-center justify-content-between mb-2">
    <h5 class="mb-0">Salas</h5>
    <span class="text-muted small">Mostrando {{ $rooms->count() }} de {{ $rooms->total() }}</span>
  </div>

  @if ($rooms->isEmpty())
    <div class="alert alert-secondary">Sin resultados.</div>
  @else
   <div class="d-flex flex-wrap gap-3">
  @foreach ($rooms as $room)
    @php
      $map = ['disponible' => 'success', 'ocupada' => 'danger', 'mantenimiento' => 'warning'];
    @endphp

    <div class="card room-card">
      <div class="card-body">
        {{-- Cabecera: nombre + badge --}}
        <div class="room-head">
          <div class="room-title">
            <div class="room-name text-truncate">{{ $room->name }}</div>
            <div class="room-line">Cap: {{ $room->capacity }} · Ocup: {{ $room->occupancy }}</div>
          </div>
          <span class="badge badge-tight text-bg-{{ $map[$room->status] ?? 'secondary' }}">
            {{ ucfirst($room->status) }}
          </span>
        </div>

        {{-- Personitas solo visual --}}
        <div class="person-icons">
          @for ($i = 1; $i <= $room->capacity; $i++)
            <i class="fa-solid fa-user person-icon {{ $i <= $room->occupancy ? 'text-danger' : 'text-success' }}"
               title="{{ $i <= $room->occupancy ? 'Ocupado' : 'Disponible' }}"></i>
          @endfor
        </div>

        {{-- Acciones --}}
        <div class="d-grid gap-1 mt-1">
          <a class="btn btn-outline-secondary btn-sm" href="{{ route('rooms.edit', $room) }}">
            <i class="fa-solid fa-pen-to-square me-1"></i> Editar
          </a>
          <form method="POST" action="{{ route('rooms.destroy', $room) }}"
                onsubmit="return confirm('¿Eliminar sala &quot;{{ $room->name }}&quot;?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm w-100">
              <i class="fa-solid fa-trash-can me-1"></i> Eliminar
            </button>
          </form>
        </div>
      </div>
    </div>
  @endforeach
</div>


    <div class="mt-3">
      {{ $rooms->links() }}
    </div>
  @endif
@endsection