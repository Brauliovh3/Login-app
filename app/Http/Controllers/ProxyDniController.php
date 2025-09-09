<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyDniController extends Controller
{
    public function consulta(Request $request)
    {
        $dni = $request->query('dni');
        if (!$dni || !preg_match('/^[0-9]{8}$/', $dni)) {
            return response()->json(['error' => 'DNI inválido'], 400);
        }

        $key = env('PERUDEVS_KEY');
        if (!$key) {
            return response()->json(['error' => 'API key no configurada'], 500);
        }

        $url = "https://api.perudevs.com/api/v1/dni/simple?document={$dni}&key={$key}";

        try {
            $resp = Http::timeout(10)->get($url);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error comunicando con PeruDevs', 'detail' => $e->getMessage()], 502);
        }

        if (!$resp->successful()) {
            return response()->json(['error' => 'Respuesta no exitosa desde PeruDevs', 'status' => $resp->status(), 'body' => $resp->body()], $resp->status());
        }

        // Devolver el JSON tal cual para que el cliente procese
        return response($resp->body(), 200)->header('Content-Type', 'application/json');
    }
}
