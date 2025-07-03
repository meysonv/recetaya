<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Actividades</title>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>📊 Reportes del sistema</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportes as $reporte)
                <tr>
                    <td>{{ $reporte->id }}</td>
                    <td>{{ $reporte->user->name ?? 'Desconocido' }}</td>
                    <td>{{ $reporte->tipo }}</td>
                    <td>
                        @if (is_array($reporte->datos))
                            @foreach ($reporte->datos as $clave => $valor)
                                <strong>{{ ucfirst($clave) }}:</strong> {{ is_array($valor) ? json_encode($valor) : $valor }}<br>
                            @endforeach
                        @else
                            {{ $reporte->datos }}
                        @endif
                    </td>
                    <td>{{ $reporte->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
