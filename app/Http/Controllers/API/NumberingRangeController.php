<?php

namespace App\Http\Controllers\API;

use App\Services\FactusApiService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NumberingRangeController extends Controller
{
    protected $factusApiService;

    public function __construct(FactusApiService $factusApiService)
    {
        $this->factusApiService = $factusApiService;
    }

    /**
     * Lista los rangos de numeración con filtros.
     */
    public function index(Request $request)
    {
        $filters = $request->query();
        $numberingRanges = $this->factusApiService->getNumberingRanges($filters);

        if (isset($numberingRanges['error'])) {
            return response()->json(['error' => $numberingRanges['error']], 500);
        }

        return response()->json($numberingRanges);
    }
}
