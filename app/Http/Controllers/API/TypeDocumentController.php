<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TypeDocument;

class TypeDocumentController extends Controller
{
    public function index(Request $request)
    {
        $fac_int = $request->query('fac_int');
        $docs = TypeDocument::where('fac_int', $fac_int)->get();
        return response()->json($docs);
    }
}
