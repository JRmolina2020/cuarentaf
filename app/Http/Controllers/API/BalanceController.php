<?php

namespace App\Http\Controllers\API;

use App\Models\Balance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBalanceByProvider($id)
    {
        $balances = Balance::where('provider_id', $id)
            ->latest()
            ->get();
        return $balances;
    }
    public function getBalanceByProviderTot($id)
    {
        $totSum = Balance::where('provider_id', $id)->sum('tot');
        return $totSum;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mytime = Carbon::now('America/Bogota');
        Balance::create([
            'code_facture' => $request['code_facture'],
            'tot' => $request['tot'],
            'date' => $mytime->toDateString(),
            'term' => $request['term'],
            'provider_id' => $request['provider_id'],
        ]);
        return response()->json(['message' => 'Factura registrada'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Balance  $balance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $balance = Balance::find($id, ['id']);
        $balance->fill([
            'code_facture' => request('code_facture'),
            'term' => request('term'),
            'tot' => request('tot'),
        ])->save();
        return response()->json(['message' => 'El gasto ha sido modificado'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Balance  $balance
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $balance = Balance::findOrFail($id);
        $balance->delete();

        return response()->json(['message' => 'Balance eliminado con éxito']);
    }
}
