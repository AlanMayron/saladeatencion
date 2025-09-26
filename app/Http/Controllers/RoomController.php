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

        $rooms = Room::query()
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // conserva ?q= en paginación

        return view('rooms.index', compact('rooms', 'q'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name'     => ['required','min:3','max:120','unique:rooms,name'],
                'capacity' => ['required','integer','min:1'],
                'status'   => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
                'name.max'      => 'El nombre no puede superar 120 caracteres.',
                'name.unique'   => 'Ya existe una sala con ese nombre.',

                'capacity.required' => 'La capacidad es obligatoria.',
                'capacity.integer'  => 'La capacidad debe ser un número entero.',
                'capacity.min'      => 'La capacidad mínima es 1.',

                'status.required' => 'El estado es obligatorio.',
                'status.in'       => 'Estado inválido (disponible, ocupada o mantenimiento).',
            ]
        );

        Room::create($data);

        return redirect()->route('rooms.index')->with('ok', 'Sala creada correctamente.');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate(
            [
                'name'     => ['required','min:3','max:120', Rule::unique('rooms','name')->ignore($room->id)],
                'capacity' => ['required','integer','min:1'],
                'status'   => ['required', Rule::in(['disponible','ocupada','mantenimiento'])],
            ],
            [
                'name.required' => 'El nombre es obligatorio.',
                'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
                'name.max'      => 'El nombre no puede superar 120 caracteres.',
                'name.unique'   => 'Ya existe una sala con ese nombre.',
                'capacity.required' => 'La capacidad es obligatoria.',
                'capacity.integer'  => 'La capacidad debe ser un número entero.',
                'capacity.min'      => 'La capacidad mínima es 1.',
                'status.required' => 'El estado es obligatorio.',
                'status.in'       => 'Estado inválido.',
            ]
        );

        $room->update($data);

        return redirect()->route('rooms.index')->with('ok', 'Sala actualizada.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('ok', 'Sala eliminada.');
    }
}
