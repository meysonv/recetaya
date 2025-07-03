<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Reporte extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'datos',
        'fecha_generado',
    ];

    protected $casts = [
    'datos' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
