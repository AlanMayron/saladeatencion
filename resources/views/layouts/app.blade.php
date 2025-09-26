<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestión de Salas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Font Awesome --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>

  <style>
    :root { --sidebar-w: 280px; }

    .sidebar-fixed{
      position: fixed; left: 0; top: 0; bottom: 0;
      width: var(--sidebar-w);
      background: #f8f9fa;
      border-right: 1px solid rgba(0,0,0,.1);
      display: flex; flex-direction: column; gap: .75rem;
      padding: .75rem; overflow-y: auto;
      z-index: 1040;
      transform: translateX(0); transition: transform .2s;
    }
    .sidebar-fixed .sidebar-header{
      display: flex; align-items: center; justify-content: space-between; gap: .5rem;
    }
    .main{
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      padding: 1rem;
      overflow-x: hidden;
      display: flex; flex-direction: column; gap: .75rem;
    }
    /* Móvil: sidebar oculta, se muestra con el botón dentro del header flex */
    @media (max-width: 991.98px){
      .sidebar-fixed{ transform: translateX(-100%); }
      .sidebar-fixed.show{ transform: translateX(0); }
      .main{ margin-left: 0; }
    }

    /* Tarjetas compactas */
    .room-card { width: 160px; }              /* <- más pequeña */
    .room-card .card-body{ padding: .5rem; }  /* menos padding */
    .person-icon{ font-size: .95rem; }
    .person-gap{ gap: .2rem; }
  </style>
</head>
<body>

  {{-- SIDEBAR --}}
  <aside id="sidebar" class="sidebar-fixed">
    <div class="sidebar-header">
      <h6 class="mb-0">Salas · Filtros</h6>
      <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarClose">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    @yield('sidebar')
  </aside>

  {{-- CONTENIDO PRINCIPAL --}}
  <main class="main">
    <div class="d-flex align-items-center justify-content-between">
      {{-- Botón hamburguesa SOLO visible en móvil, dentro del flujo flex (no se sobrepone) --}}
      <button class="btn btn-outline-secondary d-lg-none" id="sidebarOpen">
        <i class="fa-solid fa-bars"></i>
      </button>

      <div class="flex-grow-1"></div>

      {{-- espacio para acciones globales si ocupas --}}
    </div>

    @if (session('ok'))
      <div class="alert alert-success my-0">{{ session('ok') }}</div>
    @endif

    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sb   = document.getElementById('sidebar');
    const open = document.getElementById('sidebarOpen');
    const close= document.getElementById('sidebarClose');
    open?.addEventListener('click', ()=> sb?.classList.add('show'));
    close?.addEventListener('click',()=> sb?.classList.remove('show'));
  </script>

  @yield('scripts')
  @stack('scripts')
</body>
</html>
