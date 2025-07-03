<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Receta;
use Inertia\Inertia;
use App\Models\Ingrediente;
use Illuminate\Support\Facades\DB;
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
            'fecha_generado' => now(), // si lo necesitas llenar manualmente
        ]);
    }


    // Mostrar todas las recetas con relación al usuario que las subió
    public function index()
    {
        $usuarios = User::withCount('recetas')->get();
        $recetas = Receta::with('user')->get();

        $recetas = Receta::with('user')->where('aprobada', false)->get();

    
        return Inertia::render('Admin/Gestion', [
            'usuarios' => $usuarios,
            'recetas' => $recetas,
        ]);
    }

    // Actualizar receta
    public function update(Request $request, Receta $receta)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'instrucciones' => 'required|string',
            'tiempo_preparacion' => 'required|integer|min:1',
            'dificultad' => 'required|string|in:Fácil,Media,Difícil',
        ]);
    
        $antes = $receta->only('nombre', 'instrucciones', 'tiempo_preparacion', 'dificultad');
        $receta->update($validated);
    
        $this->registrarReporte('Editar receta', "Antes: " . json_encode($antes) . ", Después: " . json_encode($validated));
    
        return redirect()->back()->with('success', 'Receta actualizada correctamente.');
    }

    // Eliminar receta
    public function destroy(Receta $receta)
    {
        $nombre = $receta->nombre;
        $receta->delete();
    
        $this->registrarReporte('Eliminar receta', "Receta eliminada: {$nombre}");
    
        return redirect()->back()->with('success', 'Receta eliminada correctamente.');
    }

    // Crear nueva receta
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'instrucciones' => 'required|string',
            'tiempo_preparacion' => 'required|integer|min:1',
            'dificultad' => 'required|string|in:fácil,media,difícil',
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.nombre' => 'required|string|max:255',
            'ingredientes.*.categoria' => 'required|string|max:255',
            'ingredientes.*.cantidad' => 'required|string|max:255',
        ]);
    
        DB::transaction(function () use ($validated) {
            $receta = Receta::create([
                'user_id' => auth()->id(),
                'nombre' => $validated['nombre'],
                'instrucciones' => $validated['instrucciones'],
                'tiempo_preparacion' => $validated['tiempo_preparacion'],
                'dificultad' => $validated['dificultad'],
            ]);
    
            foreach ($validated['ingredientes'] as $ing) {
                $ingrediente = Ingrediente::firstOrCreate(
                    ['nombre' => $ing['nombre']],
                    ['categoria' => $ing['categoria']]
                );
    
                $receta->ingredientes()->attach($ingrediente->id, [
                    'cantidad' => $ing['cantidad'],
                ]);
            }
    
            $this->registrarReporte('Crear receta', "Receta: " . $receta->nombre);
        });
    
        return back()->with('success', 'Receta creada correctamente.');
    }
    
    
    public function solicitudCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'instrucciones' => 'required',
            'tiempo_preparacion' => 'required|integer',
            'dificultad' => 'required',
            'ingredientes' => 'required|array|min:1'
        ]);
    
        $receta = new Receta();
        $receta->user_id = auth()->id();
        $receta->nombre = $request->nombre;
        $receta->instrucciones = $request->instrucciones;
        $receta->tiempo_preparacion = $request->tiempo_preparacion;
        $receta->dificultad = $request->dificultad;
        $receta->aprobada = false;
        $receta->save();
    
        foreach ($request->ingredientes as $item) {
            $ingrediente = Ingrediente::firstOrCreate(
                ['nombre' => $item['nombre']],
                ['categoria' => $item['categoria']]
            );
            $receta->ingredientes()->attach($ingrediente->id, ['cantidad' => $item['cantidad']]);
        }
    
        return redirect()->back()->with('success', 'Receta enviada para aprobación.');
    }
    
    public function aprobar(Receta $receta)
    {
        $receta->aprobada = true;
        $receta->save();
        $this->registrarReporte('Aprobar receta', "Receta aprobada: {$receta->nombre}");
        return response()->json(['status' => 'aprobada']);
    }

    public function rechazar($id)
    {
        $receta = Receta::findOrFail($id);
    
        $this->registrarReporte('Rechazar receta', [
            'receta_id' => $receta->id,
            'nombre' => $receta->nombre,
        ]);
    
        // Eliminar la receta
        $receta->delete();
    
        return response()->json(['mensaje' => 'Receta rechazada correctamente']);
    }


}