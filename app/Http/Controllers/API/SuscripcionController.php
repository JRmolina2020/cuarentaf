<?php

namespace App\Http\Controllers\API;


use App\Services\FactusApiService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SuscripcionController extends Controller
{
    protected $factus;

    public function __construct(FactusApiService $factus)
    {
        $this->factus = $factus;
    }

    public function mostrar()
    {
        $resultado = $this->factus->obtenerSuscripcionActual();

        if (isset($resultado['error'])) {
            return response()->json(['error' => $resultado['error']], 500);
        }

        return response()->json($resultado);
    }
}
