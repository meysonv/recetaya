<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Receta;
use App\Models\Ingrediente;
use Illuminate\Support\Facades\DB;

class RecetaSeeder extends Seeder
{
    public function run(): void
    {
       
        DB::transaction(function () {
            $usuario = User::first() ?? User::factory()->create();

            // Lista de recetas con ingredientes
            $recetas = [

               /* [
                    'nombre' => 'Arroz con huevo frito',
                    'instrucciones' => 'Primero se hierve una taza de arroz en dos tazas de agua con sal y un poco de aceite hasta que esté completamente cocido. Mientras tanto, en una sartén se calienta un poco de aceite y se fríe un huevo hasta que la yema quede en el punto deseado. Se sirve el arroz caliente en un plato, se coloca el huevo encima, y se le puede agregar un poco de salsa de soya, sal y pimienta al gusto.',
                    'tiempo_preparacion' => 25,
                    'dificultad' => 'Fácil',
                    'ingredientes' => [
                        ['nombre' => 'Arroz', 'categoria' => 'Vegetal', 'cantidad' => '1 taza'],
                        ['nombre' => 'Huevo', 'categoria' => 'Animal', 'cantidad' => '1 unidad'],
                    ],
                ],
                */
            ];

            // Registrar recetas y sus relaciones
            foreach ($recetas as $data) {
                $receta = Receta::create([
                    'user_id' => $usuario->id,
                    'nombre' => $data['nombre'],
                    'instrucciones' => $data['instrucciones'],
                    'tiempo_preparacion' => $data['tiempo_preparacion'],
                    'dificultad' => $data['dificultad'],
                ]);

                foreach ($data['ingredientes'] as $ing) {
                    $ingrediente = Ingrediente::firstOrCreate(
                        ['nombre' => $ing['nombre']],
                        ['categoria' => $ing['categoria']]
                    );

                    $receta->ingredientes()->attach($ingrediente->id, [
                        'cantidad' => $ing['cantidad'],
                    ]);
                }
            }
        });
    }

    
}
