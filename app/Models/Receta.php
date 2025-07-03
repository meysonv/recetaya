<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $fillable = [
        'nombre',
        'instrucciones',
        'tiempo_preparacion',
        'dificultad',
        'user_id',
        'aprobada',
    ];

    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_receta')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos')->withTimestamps();
    }
    

}
