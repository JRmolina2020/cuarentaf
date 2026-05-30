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
        $docs = TypeDocument::where('fac_int', $fac_int)
            ->where('status', 1)
            ->get();
        return response()->json($docs);
    }
    public function indexAll()
    {
        return response()->json(TypeDocument::all());
    }
    public function checkCode($code)
    {
        $exists = TypeDocument::where('code', $code)->exists();
        return response()->json([
            'message' => $exists ? 'El código está registrado' : 'El código está disponible',
            'exists' => $exists
        ]);
    }
    public function store(Request $request)
    {
        $typeDocument = new TypeDocument();
        $typeDocument->prefijo = $request->prefijo;
        $typeDocument->code = $request->code;
        $typeDocument->type = $request->type;
        $typeDocument->fac_int = $request->fac_int;
        $typeDocument->save();

        return response()->json([
            'message' => 'Tipo de documento guardado exitosamente.',
            'data' => $typeDocument
        ], 201);
    }
    public function toggleStatus($id)
    {
        $document = TypeDocument::findOrFail($id);
        $document->status = !$document->status;
        $document->save();

        return response()->json([
            'message' => $document->status ? 'documento desbloqueado' : 'documento bloqueado',
            'status' => $document->status
        ]);
    }
}
