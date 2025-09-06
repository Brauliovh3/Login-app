<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CargaPasajero;

class CargaPasajeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registros = [
            [ 'informe' => '145-2023', 'resolucion' => '157-2024', 'conductor' => 'GAMARRA MOTTA Edison', 'licencia_conductor' => 'T-7686748', 'estado' => 'pendiente' ],
            [ 'informe' => '146-2023', 'resolucion' => '158-2024', 'conductor' => 'CCACHA PACHECO Joel', 'licencia_conductor' => 'Z-47853357', 'estado' => 'aprobado' ],
            [ 'informe' => '147-2023', 'resolucion' => '159-2024', 'conductor' => 'BARRIENTOS ORTIZ Angel', 'licencia_conductor' => 'Q-43047824', 'estado' => 'procesado' ],
            [ 'informe' => '148-2023', 'resolucion' => '160-2024', 'conductor' => 'ARONI ACHULLI Jorge', 'licencia_conductor' => 'T-71294241', 'estado' => 'pendiente' ],
            [ 'informe' => '149-2023', 'resolucion' => '161-2024', 'conductor' => 'SOTOMOTO PERRALTA Hugo', 'licencia_conductor' => 'T-4596271', 'estado' => 'aprobado' ],
            [ 'informe' => '150-023', 'resolucion' => '162-2024', 'conductor' => 'MOREANO CARRION Efrain', 'licencia_conductor' => 'T-44832032', 'estado' => 'procesado' ],
            [ 'informe' => '151-2023', 'resolucion' => '163-2024', 'conductor' => 'SEQUEIROS OROS Nestor', 'licencia_conductor' => 'Q-42192543', 'estado' => 'pendiente' ],
            [ 'informe' => '153-2023', 'resolucion' => '164-2024', 'conductor' => 'PALOMINO CONDORI Che', 'licencia_conductor' => 'Z-44497073', 'estado' => 'aprobado' ],
            [ 'informe' => '154-2023', 'resolucion' => '165-2024', 'conductor' => 'BEDIA LAYME Edward', 'licencia_conductor' => 'Q-4795539', 'estado' => 'procesado' ],
            [ 'informe' => '155-2023', 'resolucion' => '166-2024', 'conductor' => 'YANAC CABEZAS Richard', 'licencia_conductor' => 'Q-41592717', 'estado' => 'pendiente' ],
            [ 'informe' => '156-2023', 'resolucion' => '167-2024', 'conductor' => 'ALVARO TRITTO Avelino', 'licencia_conductor' => 'Z-41592717', 'estado' => 'aprobado' ],
            [ 'informe' => '157-2023', 'resolucion' => '168-2024', 'conductor' => 'ALATA PORTOCARRERO Wilber', 'licencia_conductor' => 'T-4393918', 'estado' => 'procesado' ],
            [ 'informe' => '158-2023', 'resolucion' => '169-2024', 'conductor' => 'VARGAS MAMANI Jaime', 'licencia_conductor' => 'T-47393018', 'estado' => 'pendiente' ],
            [ 'informe' => '159-2023', 'resolucion' => '170-2024', 'conductor' => 'GUEVARA RIOS José', 'licencia_conductor' => 'T-4329618', 'estado' => 'aprobado' ],
            [ 'informe' => '160-2023', 'resolucion' => '171-2024', 'conductor' => 'LAURA GALLEGOS Samuel Bernabé', 'licencia_conductor' => 'Z-42810044', 'estado' => 'procesado' ],
            [ 'informe' => '161-2023', 'resolucion' => '172-2024', 'conductor' => 'PEREZ MUÑOZ Jorge', 'licencia_conductor' => 'F-31015007', 'estado' => 'pendiente' ],
            [ 'informe' => '162-2023', 'resolucion' => '173-2024', 'conductor' => 'QUIVIO VALENZUELA Claudio', 'licencia_conductor' => 'T-4339618', 'estado' => 'aprobado' ],
            [ 'informe' => '163-2023', 'resolucion' => '174-2024', 'conductor' => 'TITTO MAMANI David', 'licencia_conductor' => 'F-42027916', 'estado' => 'procesado' ],
            [ 'informe' => '164-2023', 'resolucion' => '175-2024', 'conductor' => 'CCASA HERMOSA Justo', 'licencia_conductor' => 'T-44969858', 'estado' => 'pendiente' ],
        ];

        foreach ($registros as $data) {
            CargaPasajero::create($data);
        }
    }
}
