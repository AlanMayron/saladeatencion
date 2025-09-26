<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perPage = (int) $request->input('perPage', 12);
        $perPage = max(4, min($perPage, 48));

        $rooms = Room::query()
            ->when($q, fn($query) => $query->where('name', 'ILIKE', "%{$q}%"))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('rooms.index', compact('rooms', 'q'));
    }

    public function create()
    {
        return view('rooms.create');
    }

public function store(Request $request)
{
    $data = $request->validate([
        'name'      => ['required','min:3','max:120','unique:rooms,name'],
        'capacity'  => ['required','integer','min:1'],
        'status'    => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
        'occupancy' => ['nullable','integer','min:0'],
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
        return view('rooms.edit', compact('room'));
    }

public function update(Request $request, Room $room)
{
    $data = $request->validate([
        'name'      => ['required','min:3','max:120', Rule::unique('rooms','name')->ignore($room->id)],
        'capacity'  => ['required','integer','min:1'],
        'status'    => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
        'occupancy' => ['nullable','integer','min:0'],
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
}
