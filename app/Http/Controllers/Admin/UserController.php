<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Receta;
use Inertia\Inertia;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
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

    public function index()
    {

        $usuarios = User::withCount('recetas')->get(); //lama todos los usuarios
        //$usuarios = User::withCount('recetas')->paginate(10);   //paginando 10 por página no sé si sirve por el scroll

        return Inertia::render('Admin/Gestion', [
            'usuarios' => $usuarios
        ]);
    }
    
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
    
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
    
        $nombre = $usuario->name;
        $usuario->delete();
    
        $this->registrarReporte('Eliminar usuario', "Usuario eliminado: {$nombre}");
    
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
    
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'rol' => 'required|in:admin,cliente',
        ]);
    
        $datosAntes = $user->only('name', 'email', 'rol');
        $user->update($request->only('name', 'email', 'rol'));
    
        $this->registrarReporte('Editar usuario', "Antes: " . json_encode($datosAntes) . ", Después: " . json_encode($request->only('name', 'email', 'rol')));
    
        return redirect()->route('admin.gestion')->with('success', 'Usuario actualizado correctamente.');
    }
    
    public function gestion()
    {
        return Inertia::render('Admin/Gestion', [
            'usuarios' => User::withCount('recetas')->get(),
            'recetas' => Receta::with('user')->get(),
            'reportes' => \App\Models\Reporte::with('user')->latest()->get(),
        ]);
    }


}

//punto de control
//punto de retorno
//aqui se comenzo