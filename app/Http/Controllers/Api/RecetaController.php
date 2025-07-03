<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receta;
use Illuminate\Http\Request;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;


class RecetaController extends Controller
{
    private function registrarReporte($tipo, $datos)
    {
        Reporte::create([
            'user_id' => Auth::id(),
            'tipo' => $tipo,
            'datos' => $datos,
            'fecha_generado' => now(), 
        ]);
    }

    public function index(Request $request)
    
    {
        $query = Receta::with(['ingredientes', 'user']);
    
        // Si se pidio solo las recetas pendientes de aprobación
        
        if ($request->has('pendientes')) {
            $query->where('aprobada', $request->pendientes == 1 ? false : true);
        } else {
            $query->where('aprobada', true); // por defecto solo recetas aprobadas
        }
        
        // Filtro por ingredientes separados por espacio
        if ($request->filled('ingrediente')) {
            $ingredientes = explode(' ', $request->input('ingrediente'));
    
            foreach ($ingredientes as $ingrediente) {
                $query->whereHas('ingredientes', function ($q) use ($ingrediente) {
                    $q->where('nombre', 'LIKE', '%' . $ingrediente . '%');
                });
            }
        }
    
        // Filtro por categoría
        if ($request->filled('categoria')) {
            $categoria = $request->input('categoria');
            $query->whereHas('ingredientes', function ($q) use ($categoria) {
                $q->where('categoria', $categoria);
            });
        }
    
        $recetas = $query->get();
    
        // Marcar favoritos si hay usuario autenticado
        if (Auth::check()) {
            $favoritosIds = Auth::user()->favoritos()->pluck('receta_id')->toArray();
    
            $recetas->each(function ($receta) use ($favoritosIds) {
                $receta->es_favorito = in_array($receta->id, $favoritosIds);
            });
        }
    
        return $recetas;
    }
    
    public function toggleFavorito($id)
    {
        $user = auth()->user();
        $receta = Receta::findOrFail($id);
    
        if ($user->favoritos()->where('receta_id', $id)->exists()) {
            $user->favoritos()->detach($id);
            $this->registrarReporte('Quitar favorito', "Receta: {$receta->nombre}");
            return response()->json(['status' => 'removed']);
        } else {
            $user->favoritos()->attach($id);
            $this->registrarReporte('Agregar favorito', "Receta: {$receta->nombre}");
            return response()->json(['status' => 'added']);
        }
    }
    
    public function listarFavoritos()
    {
        $user = auth()->user();
        $favoritos = $user->favoritos()->select('recetas.id', 'nombre', 'tiempo_preparacion', 'dificultad')->get();
        return response()->json($favoritos);
    }

    public function verDetalle($id)
    {
        $user = auth()->user();
        $receta = Receta::with('ingredientes')->findOrFail($id);

    // Agrega si es favorito
        $receta->es_favorito = $user->favoritos()->where('receta_id', $id)->exists();

    return response()->json($receta);
    }

}