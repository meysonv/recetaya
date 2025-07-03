<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
    ];

    public function recetas()
    {
        return $this->belongsToMany(Receta::class, 'ingrediente_receta')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
