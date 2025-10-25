<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facultad extends Model
{
    use HasFactory;

    protected $table = 'facultades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
    ];

    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class);
    }
}