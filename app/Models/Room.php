<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // <-- FALTA ESTA LÍNEA

class Room extends Model
{
    use HasFactory;

    public function scopeLocation(Builder $query, $location): Builder
    {
        if (empty($location)) return $query;
        return $query->where('location', $location);
    }

    protected $fillable = [
        'name', 'capacity', 'status', 'occupancy', 'location',
    ];
}
