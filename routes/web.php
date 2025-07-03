<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\RecetaController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use App\Models\Receta;
use App\Http\Controllers\Admin\RecetaController as AdminRecetaController;
use App\Http\Controllers\Admin\ReporteController;


// Ruta principal
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Redirección por rol
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->rol) {
        'admin' => redirect()->route('admin.dashboard'),
        'cliente' => redirect()->route('cliente.dashboard'),
        default => redirect()->route('home'),
    };
})->middleware(['auth'])->name('dashboard');

//favoritos cualuier rol autenticado
Route::middleware(['auth'])->post('/favoritos/{id}', [RecetaController::class, 'toggleFavorito']);
//ruta modal favoritos
Route::middleware('auth')->get('/api/favoritos', [RecetaController::class, 'listarFavoritos']);
// Ruta modal para ver detalle de receta
Route::middleware('auth')->get('/api/recetas/{id}', [RecetaController::class, 'verDetalle']);

// ADMIN ROUTES
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Vistas principales
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/gestion', [UserController::class, 'gestion'])->name('gestion');

    // Usuarios
    Route::get('/usuario', [UserController::class, 'index'])->name('usuarios');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuario/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');

    // Recetas
    Route::get('/recetas', [AdminRecetaController::class, 'index'])->name('recetas');
    Route::put('/recetas/{receta}', [AdminRecetaController::class, 'update'])->name('recetas.update');
    Route::delete('/recetas/{receta}', [AdminRecetaController::class, 'destroy'])->name('recetas.destroy');

    //crear receta
    Route::post('/recetas', [AdminRecetaController::class, 'store'])->name('recetas.store');

    //reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes'); //metodo 1
    //Exportar reportes a PDF
    Route::get('/reportes/exportar', [ReporteController::class, 'exportarPDF'])->name('reportes.exportar');
    //aprobar receta
    Route::put('/recetas/{receta}/aprobar', [AdminRecetaController::class, 'aprobar'])->name('recetas.aprobar');
    //rechazar receta
    Route::delete('/recetas/{id}/rechazar', [AdminRecetaController::class, 'rechazar'])->name('recetas.rechazar');



});

// Route::get('/reportes', function () { //metodo 2
//     return Inertia::render('Admin/Reportes', [
//         'reportes' => \App\Models\Reporte::with('user')->orderByDesc('created_at')->get(),
//     ]);
// })->middleware(['auth', 'role:admin'])->name('admin.reportes');


// CLIENTE ROUTES
Route::middleware(['auth', 'verified', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Cliente/Dashboard'))->name('dashboard');

    //solicitudes cliente
    Route::post('/solicitudes-receta', [AdminRecetaController::class, 'solicitudCliente']);

    // //favoritos
    // Route::post('/favoritos/{id}', [RecetaController::class, 'toggleFavorito'])->middleware('auth');
});

// Rutas extra
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// API pública (no requiere auth)
Route::get('/api/recetas', [RecetaController::class, 'index']);

Route::put('/recetas/{receta}/aprobar', [AdminRecetaController::class, 'aprobar'])->name('recetas.aprobar');

