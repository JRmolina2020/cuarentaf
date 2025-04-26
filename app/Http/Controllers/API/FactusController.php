<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\FactusApiService;
use App\Services\DataService;
use App\Models\Facture;
use Carbon\Carbon;
use App\Http\Controllers\Controller;


class FactusController extends Controller
{
    protected $factusService;
    protected $DataService;

    public function __construct(FactusApiService $factusService, DataService $DataService)
    {
        $this->factusService = $factusService;
        $this->DataService = $DataService;
    }

    public function sendInvoice(Request $request)
    {
        $id = $request->input('id');
        // $id = 26;
        $facture = $this->DataService->getFactureWithClientAndResolution($id);
        $items = $this->DataService->getProductsItems($id);

        //tto
        $date = Carbon::parse($facture->date_facture); // Convierte la fecha en un objeto Carbon
        $newDate = $date->addDay(); // Suma un día a la fecha
        $newDateFormatted = $newDate->format('Y-m-d');

        try {
            $invoiceData = [
                "numbering_range_id" =>  8,
                "reference_code" => 'HKS' . $facture->id,
                "observation" => "",
                "payment_form" => "1",
                "payment_due_date" =>  $newDateFormatted,
                "payment_method_code" => "10",
                "billing_period" => [
                    "start_date" => $facture->date_facture,
                    "start_time" => "00:00:00",
                    "end_date" => $newDateFormatted,
                    "end_time" => "23:59:59"
                ],
                "customer" => [
                    "identification" => "222222222222",
                    "dv" => 0,
                    "company" => "",
                    "trade_name" => "",
                    "names" => "cliente ocasional",
                    "address" => "sin presentar",
                    "email" => "sinpresentar@gmail.com",
                    "phone" => "1234567890",
                    "legal_organization_id" => "2",
                    "tribute_id" => "21",
                    "identification_document_id" => "3",
                    "municipality_id" => 404
                ],
                "items" => $items
            ];

            // dd($invoiceData);
            $response = $this->factusService->sendInvoice($invoiceData);
            if (isset($response['data']['bill']['cufe'])) {
                $this->updateCufe($response, $id);
            }
            return ($response);
            // return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateCufe($response, $id)
    {
        $cufe = $response['data']['bill']['cufe'];
        $validated = $response['data']['bill']['validated'];
        $number = $response['data']['bill']['number'];
        $factura = Facture::where('id', $id)->first();

        if ($factura) {
            $factura->cufe = $cufe;
            $factura->validated_at = $validated;
            $factura->numberf = $number;
            $factura->save();

            return response()->json(['message' => 'CUFE actualizado con éxito']);
        } else {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }
    }
}
