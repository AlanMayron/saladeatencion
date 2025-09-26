@php
  $statuses = ['disponible' => 'Disponible', 'ocupada' => 'Ocupada', 'mantenimiento' => 'Mantenimiento'];
  $cap = old('capacity', $room->capacity ?? 1);
  $occ = old('occupancy', $room->occupancy ?? 0);
  $st  = old('status', $room->status ?? 'disponible');
@endphp

@if ($errors->any())
  <div class="alert alert-danger">
    <strong>Corrige los errores:</strong>
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="mb-3">
  <label class="form-label">Nombre</label>
  <input name="name" value="{{ old('name', $room->name ?? '') }}" class="form-control">
  @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

<div class="row g-3">
  <div class="col-6">
    <label class="form-label">Capacidad</label>
    <input type="number" min="1" name="capacity" id="capacity-input" value="{{ $cap }}" class="form-control">
    @error('capacity')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>

  <div class="col-6">
    <label class="form-label">Ocupación</label>
    <div class="input-group">
      <input type="number" min="0" name="occupancy" id="occupancy-input" value="{{ $occ }}" class="form-control">
      <button type="button" class="btn btn-outline-secondary" id="apply-occ">Aplicar</button>
    </div>
    @error('occupancy')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Estado</label>
  <select name="status" id="status-select" class="form-select">
    @foreach ($statuses as $value => $label)
      <option value="{{ $value }}" @selected($st === $value)>{{ $label }}</option>
    @endforeach
  </select>
  @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
</div>

{{-- Opcional: vista/edición por personitas que sincroniza el número --}}
<div class="mb-2">
  <div class="form-label">Personas (click alterna)</div>
  <div id="persons" class="d-flex flex-wrap" style="gap:.25rem;"></div>
  <div class="form-text">Rojo = ocupado · Verde = disponible</div>
</div>

@push('scripts')
<script>
(function(){
  const persons   = document.getElementById('persons');
  const capInput  = document.getElementById('capacity-input');
  const occInput  = document.getElementById('occupancy-input');
  const applyBtn  = document.getElementById('apply-occ');

  function render(cap, occ){
    persons.innerHTML = '';
    cap = Math.max(1, parseInt(cap||'1',10));
    occ = Math.min(Math.max(0, parseInt(occ||'0',10)), cap);

    for (let i=1;i<=cap;i++){
      const occupied = i <= occ;
      const icon = document.createElement('i');
      icon.className = 'fa-solid fa-user ' + (occupied ? 'text-danger' : 'text-success');
      icon.title = occupied ? 'Ocupado' : 'Disponible';
      icon.dataset.occupied = occupied ? '1' : '0';
      icon.style.cursor = 'pointer';
      icon.addEventListener('click', () => {
        const isOcc = icon.dataset.occupied === '1';
        icon.dataset.occupied = isOcc ? '0' : '1';
        icon.classList.toggle('text-danger', !isOcc);
        icon.classList.toggle('text-success', isOcc);
        // recuenta
        const current = [...persons.querySelectorAll('.fa-user')].filter(x => x.dataset.occupied === '1').length;
        occInput.value = current;
      });
      persons.appendChild(icon);
    }
    occInput.value = occ;
  }
  applyBtn.addEventListener('click', () => render(capInput.value, occInput.value));
  capInput.addEventListener('input', () => render(capInput.value, occInput.value));
  occInput.addEventListener('input', () => render(capInput.value, occInput.value));
  render(capInput.value, occInput.value);
})();
</script>
@endpush
