<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $query = Reporte::with('user');

        // Filtros combinables
        if ($request->filled('usuario')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->usuario . '%');
            });
        }

        if ($request->filled('accion')) {
            $query->where('tipo', 'like', '%' . $request->accion . '%');
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $reportes = $query->latest()->get();

        return Inertia::render('Admin/Reportes', [
            'reportes' => $reportes,
        ]);
    }
    
    public function exportarPDF(Request $request)
    {
        $query = Reporte::with('user');
    
        if ($request->filled('usuario')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->usuario . '%');
            });
        }
    
        if ($request->filled('accion')) {
            $query->where('tipo', 'like', '%' . $request->accion . '%');
        }
    
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }
    
        $reportes = $query->latest()->get();
    
        $pdf = Pdf::loadView('pdf.reportes', compact('reportes'));
        $nombreArchivo = 'reportes_' . now()->format('Ymd_His') . '.pdf';
    
        return $pdf->download($nombreArchivo);
    }
}
