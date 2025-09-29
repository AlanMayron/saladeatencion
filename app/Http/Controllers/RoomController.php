<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
{
    $q        = $request->string('q')->toString();
    $perPage  = (int) $request->input('perPage', 12);
    $location = $request->input('location'); // puede venir null

    // Para popular el <select> de ubicaciones:
    $locations = \App\Models\Room::query()
        ->whereNotNull('location')
        ->distinct()
        ->orderBy('location')
        ->pluck('location');

    $rooms = \App\Models\Room::query()
        ->when($q, fn($qb) =>
            $qb->where(function($qq){
                $q = request('q');
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('code', 'like', "%{$q}%"); // si tienes code u otro campo
            })
        )
        ->location($location) // usando el scope de arriba
        ->paginate($perPage)
        ->withQueryString(); // mantiene filtros al paginar

    return view('rooms.index', compact('rooms', 'q', 'perPage', 'location', 'locations'));
}

    public function create()
{
    $locations = Room::whereNotNull('location')->distinct()->orderBy('location')->pluck('location');
    return view('rooms.create', compact('locations'));
}

public function store(Request $request)
{
    $data = $request->validate([
        'name'      => ['required','min:3','max:120','unique:rooms,name'],
        'capacity'  => ['required','integer','min:1'],
        'status'    => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
        'occupancy' => ['nullable','integer','min:0'],
        'location' => ['nullable','string','max:120'],
    ]);

    $data['occupancy'] = min((int)($data['occupancy'] ?? 0), (int)$data['capacity']);
    if ($data['status'] !== 'mantenimiento') {
        $data['status'] = ($data['occupancy'] >= $data['capacity']) ? 'ocupada' : 'disponible';
    }

    Room::create($data);

    // ⬅️ Volver al inicio (listado) sin parámetros
    return redirect()->route('rooms.index')->with('ok', 'Sala creada.');
}


    public function edit(Room $room)
{
    $locations = Room::whereNotNull('location')->distinct()->orderBy('location')->pluck('location');
    return view('rooms.edit', compact('room', 'locations'));
}

public function update(Request $request, Room $room)
{
    $data = $request->validate([
        'name'      => ['required','min:3','max:120', Rule::unique('rooms','name')->ignore($room->id)],
        'capacity'  => ['required','integer','min:1'],
        'status'    => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
        'occupancy' => ['nullable','integer','min:0'],
        'location' => ['nullable','string','max:120'],
    ]);

    // Normaliza ocupación y ajusta estado (si no es mantenimiento)
    $data['occupancy'] = min((int)($data['occupancy'] ?? $room->occupancy), (int)$data['capacity']);
    if ($data['status'] !== 'mantenimiento') {
        $data['status'] = ($data['occupancy'] >= $data['capacity']) ? 'ocupada' : 'disponible';
    }

    $room->update($data);

    // Volver al listado con mensaje
    return redirect()->route('rooms.index')->with('ok', 'Sala actualizada.');
}

    public function destroy(Room $room)
    {
        $name = $room->name;
        $room->delete();

        return redirect()
            ->route('rooms.index', ['q' => $name])
            ->with('ok', 'Sala eliminada.');
    }

    // Si usas este endpoint desde tarjetas para guardar solo ocupación:
    public function setOccupancy(Request $request, Room $room)
    {
        $data = $request->validate([
            'occupancy' => ['required','integer','min:0','max:'.$room->capacity],
        ]);

        $newOcc = (int) $data['occupancy'];
        $newStatus = $room->status;
        if ($newStatus !== 'mantenimiento') {
            $newStatus = ($newOcc >= $room->capacity) ? 'ocupada' : 'disponible';
        }

        $room->update([
            'occupancy' => $newOcc,
            'status'    => $newStatus,
        ]);

        return redirect()
            ->route('rooms.index', ['q' => $room->name])
            ->with('ok', 'Ocupación actualizada.');
    }
    public function scopeLocation($q, ?string $location)
{
    if (!$location) return $q;
    // Si usarás select exacto:
    return $q->where('location', $location);
    
    // Si prefieres búsqueda por subcadena:
    // return $q->whereRaw('LOWER(location) LIKE ?', ['%'.mb_strtolower($location).'%']);
}

}
