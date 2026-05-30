<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{

    public function index()
    {
        $clients = DB::table('clients')
            ->select('id', 'nit', 'dv', 'fullname', 'phone', 'email', 'status')
            ->orderBy('id', 'desc')->get();
        return $clients;
    }
    public function indexActive()
    {
        $clients = DB::table('clients')
            ->select('id', 'nit', 'dv', 'fullname', 'phone', 'email', 'status')
            ->where('status', '=', '1')
            ->orderBy('id', 'desc')->get();
        return $clients;
    }

    public function store(Request $request)
    {
        Client::create([
            'nit' => $request['nit'],
            'dv' => $request['dv'],
            'fullname' => $request['fullname'],
            'phone' => $request->filled('phone') ? $request->input('phone') : '11111111',
            'email' => $request->filled('email') ? $request->input('email') : 'notiene@gmail.com',

        ]);
        return response()->json(['message' => 'Cliente registrado'], 200);
    }

    public function update($id)
    {
        $client = Client::find($id, ['id']);
        $client->fill([
            'nit' => request('nit'),
            'dv' => request('dv'),
            'fullname' => request('fullname'),
            'phone' => request('phone'),
            'email' => request('email'),

        ])->save();
        return response()->json(['message' => 'El cliente ha sido modificado'], 201);
    }
    public function destroy($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(["message" => "Cliente no encontrado"], 404);
        }
    }
    public function toggleStatus($id)
    {
        $client = Client::findOrFail($id);
        $client->status = !$client->status;
        $client->save();

        return response()->json([
            'message' => $client->status ? 'cliente desbloqueado' : 'cliente bloqueado',
            'status' => $client->status
        ]);
    }
}
