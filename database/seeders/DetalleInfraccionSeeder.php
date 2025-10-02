<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleInfraccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Función auxiliar para obtener ID de infracción por código
        $getInfraccionId = function($codigo) {
            return DB::table('infracciones')->where('codigo_infraccion', $codigo)->value('id');
        };

        $detalles = [
            // Detalles para F.4 - Obstrucción de fiscalización (Transportista)
            [
                'codigo_infraccion' => 'F.4',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'Negarse a entregar la información o documentación correspondiente al vehículo, a su habilitación como conductor, al servicio que presta o actividad de transporte que realiza al ser requerido para ello.',
                'condiciones_especiales' => 'Aplica cuando el transportista se niega a proporcionar documentación requerida por la autoridad competente',
                'observaciones' => 'Constituye obstrucción a la labor de fiscalización'
            ],
            [
                'codigo_infraccion' => 'F.4',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'Brindar intencionalmente información no conforme, a la autoridad competente, durante la fiscalización con el propósito de hacerla incurrir en error respecto de la autorización para prestar el servicio, de la habilitación del vehículo o la del conductor.',
                'condiciones_especiales' => 'Aplica cuando se proporciona información falsa o errónea de manera intencional',
                'observaciones' => 'Información deliberadamente incorrecta para evadir fiscalización'
            ],
            [
                'codigo_infraccion' => 'F.4',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'Incurrir en actos de simulación, suplantación u otras conductas destinadas a hacer incurrir en error a la autoridad competente respecto de la autorización para prestar el servicio, o respecto de la habilitación del vehículo o la del conductor.',
                'condiciones_especiales' => 'Aplica cuando se simula o suplanta documentación o identidades para evadir controles',
                'observaciones' => 'Incluye falsificación de documentos o suplantación de identidad'
            ],

            // Detalles para F.5 - Infracciones del Generador de carga
            [
                'codigo_infraccion' => 'F.5',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'Contratar el servicio de transporte con un transportista que no se encuentra autorizado, o cuya autorización es para realizar servicio de transporte privado de mercancías.',
                'condiciones_especiales' => 'El generador de carga debe verificar la autorización del transportista antes de contratar',
                'observaciones' => 'Responsabilidad del contratante verificar las credenciales del transportista'
            ],
            [
                'codigo_infraccion' => 'F.5',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'Permitir o utilizar la vía pública como lugar habitual o constante para la carga y/o descarga de mercancías.',
                'condiciones_especiales' => 'No se permite el uso permanente de vías públicas para actividades de carga/descarga',
                'observaciones' => 'Diferente a uso ocasional o de emergencia'
            ],
            [
                'codigo_infraccion' => 'F.5',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'Exigir que el transportista cuente con la autorización especial de la autoridad vial que corresponda cuando transporte bienes cuyas dimensiones o peso superen los máximos establecidos por el RNV.',
                'condiciones_especiales' => 'Aplica para cargas que excedan las dimensiones o peso máximo permitido',
                'observaciones' => 'Requiere autorización especial de autoridad vial competente'
            ],

            // Detalles para F.6 - Obstrucción de fiscalización (Conductor)
            [
                'codigo_infraccion' => 'F.6',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'Negarse a entregar la información o documentación correspondiente al vehículo, a su habilitación como conductor, al servicio que presta o actividad de transporte que realiza, al ser requerido para ello.',
                'condiciones_especiales' => 'El conductor debe portar y exhibir toda la documentación requerida cuando sea solicitada',
                'observaciones' => 'Incluye licencia, documentos del vehículo y documentos del servicio'
            ],
            [
                'codigo_infraccion' => 'F.6',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'Brindar intencionalmente información no conforme, a la autoridad competente, durante la fiscalización con el propósito de hacerla incurrir en error respecto de la autorización para prestar el servicio, de la habilitación del vehículo o la del conductor.',
                'condiciones_especiales' => 'La información debe ser veraz y corresponder a la realidad',
                'observaciones' => 'Incluye datos falsos sobre rutas, carga, pasajeros, etc.'
            ],
            [
                'codigo_infraccion' => 'F.6',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'Realizar maniobras evasivas con el vehículo para evitar la fiscalización.',
                'condiciones_especiales' => 'Incluye cambios de ruta, aceleración, fuga u otras maniobras para evadir control',
                'observaciones' => 'Puede constituir agravante si pone en riesgo la seguridad'
            ],
            [
                'codigo_infraccion' => 'F.6',
                'subcategoria' => 'd)',
                'descripcion_detallada' => 'Incurrir en actos de simulación, suplantación u otras conductas destinadas a hacer incurrir en error a la autoridad competente respecto de la autorización para prestar el servicio, o respecto de la habilitación del vehículo o la del conductor.',
                'condiciones_especiales' => 'Incluye uso de documentos falsos, suplantación de identidad o simulación de servicios',
                'observaciones' => 'Constituye falta grave que puede derivar en proceso penal'
            ],

            // Detalles para I.1 - Falta de documentación (Transportista)
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'No portar el manifiesto de usuarios, en el transporte de personas, cuando este no sea electrónico.',
                'condiciones_especiales' => 'Aplica cuando el sistema no es electrónico y se requiere manifiesto físico',
                'observaciones' => 'Documento obligatorio para control de pasajeros'
            ],
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'No portar la hoja de ruta manual o electrónica, según corresponda.',
                'condiciones_especiales' => 'Documento que acredita la ruta autorizada para el servicio',
                'observaciones' => 'Esencial para verificar cumplimiento de ruta autorizada'
            ],
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'En el servicio de transporte de mercancías no portar la guía de remisión del transportista y, de ser el caso, manifiesto de carga.',
                'condiciones_especiales' => 'Documentos obligatorios para transporte de mercancías',
                'observaciones' => 'Permite verificar origen, destino y naturaleza de la carga'
            ],
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'd)',
                'descripcion_detallada' => 'No portar el documento de habilitación del vehículo.',
                'condiciones_especiales' => 'Documento que acredita que el vehículo está autorizado para prestar servicio público',
                'observaciones' => 'Fundamental para verificar legalidad del servicio'
            ],
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'e)',
                'descripcion_detallada' => 'No portar el certificado de Inspección Técnica Vehicular.',
                'condiciones_especiales' => 'Certificado que acredita que el vehículo cumple condiciones técnicas de seguridad',
                'observaciones' => 'Debe estar vigente y corresponder al vehículo'
            ],
            [
                'codigo_infraccion' => 'I.1',
                'subcategoria' => 'f)',
                'descripcion_detallada' => 'No portar el certificado del seguro obligatorio de accidente de tránsito o CAT cuando corresponda.',
                'condiciones_especiales' => 'Seguro obligatorio para vehículos de servicio público',
                'observaciones' => 'Debe estar vigente y cubrir el vehículo específico'
            ],

            // Detalles para I.2 - Falta de información al usuario (Conductor)
            [
                'codigo_infraccion' => 'I.2',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'No exhibir en cada vehículo habilitado al servicio de transporte público de personas, la modalidad del servicio, según corresponda, la razón social y el nombre comercial si lo tuviera.',
                'condiciones_especiales' => 'Información debe ser visible y legible para los usuarios',
                'observaciones' => 'Permite a usuarios identificar el servicio y la empresa'
            ],
            [
                'codigo_infraccion' => 'I.2',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'En el servicio de transporte provincial de personas, no colocar en lugar visible para el usuario, la información sobre las tarifas vigentes y la ruta autorizada.',
                'condiciones_especiales' => 'Aplica específicamente para transporte provincial de personas',
                'observaciones' => 'Información esencial para protección del usuario'
            ]
        ];

        foreach ($detalles as $detalle) {
            // Obtener el ID de la infracción basado en el código
            $infraccionId = $getInfraccionId($detalle['codigo_infraccion']);
            
            if ($infraccionId) {
                DB::table('detalle_infraccion')->updateOrInsert(
                    [
                        'infraccion_id' => $infraccionId,
                        'subcategoria' => $detalle['subcategoria']
                    ],
                    [
                        'infraccion_id' => $infraccionId,
                        'subcategoria' => $detalle['subcategoria'],
                        'descripcion_detallada' => $detalle['descripcion_detallada'],
                        'condiciones_especiales' => $detalle['condiciones_especiales'],
                        'observaciones' => $detalle['observaciones'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
