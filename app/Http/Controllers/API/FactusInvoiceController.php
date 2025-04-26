<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\FactusApiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log; // Importar Log para manejar los logs

class FactusInvoiceController extends Controller
{
    protected $factusService;

    public function __construct(FactusApiService $factusService)
    {
        $this->factusService = $factusService;
    }

    /**
     * Obtener las facturas de la API de Factus.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInvoices(Request $request)
    {

        // Verificar si el parámetro 'filter[identification]' está presente en la URL
        $identification = $request->query('filter')['identification'];

        if (!$identification) {
            Log::error('La identificación es obligatoria.');
            return response()->json(['error' => 'La identificación es obligatoria.'], 400);
        }

        try {
            // Llamar al servicio para obtener las facturas usando el identificador
            $response = $this->factusService->getInvoices($identification);

            // Verificar si la respuesta tiene un error
            if (isset($response['error'])) {
                Log::error('Error al obtener facturas: ' . $response['error']);
                return response()->json(['error' => $response['error']], 500);
            }

            // Si todo está bien, devolver la respuesta de las facturas
            return response()->json($response);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y registrar el error
            Log::error('Excepción al obtener facturas: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
