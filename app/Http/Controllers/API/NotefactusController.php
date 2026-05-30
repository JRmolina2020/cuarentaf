<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\FactusApiService;
use App\Services\DataService;
use App\Models\CreditNote;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\TypeDocument;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use App\Http\Controllers\API\FactureController;



class NoteFactusController extends Controller
{
    protected $factusService;
    protected $DataService;


    public function __construct(FactusApiService $factusService, DataService $DataService)
    {
        $this->factusService = $factusService;
        $this->DataService = $DataService;
    }

    public function sendNote(Request $request)
    {
        $id = $request->input('id');
        $facture = $this->DataService->getFactureWithClientAndResolution($id);
        $items = $this->DataService->getProductsItems($id);
        $date = Carbon::parse($facture->date_facture);
        $newDate = $date->addDay();
        $newDateFormatted = $newDate->format('Y-m-d');

        // traer tipo de documento electronico 
        $code = TypeDocument::where('type', 'Elec')
            ->value('code');

        try {
            $invoiceData = [
                "send_email" => true,
                "numbering_range_id" =>  '',
                "correction_concept_code" => 2,
                "customization_id" => 20,
                "bill_id" => $facture->bill_id,
                "reference_code" => $facture->numberf,
                "observation" => "Nota crédito generada para la anulación total de la factura electrónica referenciada.",
                "payment_method_code" => "10",

                "billing_period" => [
                    "start_date" => $facture->date_facture,
                    "start_time" => "00:00:00",
                    "end_date" => $newDateFormatted,
                    "end_time" => "23:59:59"
                ],
                "customer" => [
                    "identification" => $facture->nit,
                    "dv" => $facture->dv,
                    "company" => "",
                    "trade_name" => "",
                    "names" => $facture->fullname,
                    "address" => "sin presentar",
                    "email" => $facture->email,
                    "phone" => "1234567890",
                    "legal_organization_id" => "2",
                    "tribute_id" => "21",
                    "identification_document_id" => "3",
                    "municipality_id" => 404
                ],
                "items" => $items
            ];

            $response = $this->factusService->sendNote($invoiceData);
            // return response()->json($response, 200, [], JSON_PRETTY_PRINT);
            $this->storeCreditNote($response);
            return response()->json($response);
        } catch (ClientException $e) {

            $response = $e->getResponse();
            $body = $response->getBody()->getContents();

            return response()->json(json_decode($body, true), 422, [], JSON_PRETTY_PRINT);
        }
    }
    public function storeCreditNote($response)
    {
        $credit = $response['data']['credit_note'];

        $validated = Carbon::createFromFormat(
            'd-m-Y h:i:s A',
            $credit['validated']
        )->format('Y-m-d H:i:s');

        CreditNote::create([
            'number' => $credit['number'],
            'reference_code' => $credit['reference_code'],
            'status' => $credit['status'],
            'qr' => $credit['qr'],
            'cude' => $credit['cude'],
            'validated' => $validated,
            'gross_value' => $credit['gross_value'],
            'taxable_amount' => $credit['taxable_amount'],
            'tax_amount' => $credit['tax_amount'],
            'discount_amount' => $credit['discount_amount'],
            'surcharge_amount' => $credit['surcharge_amount'],
            'total' => $credit['total'],
            'observation' => $credit['observation']
        ]);
        $factura = Facture::where('bill_id', $credit['bill_id'])->first();

        if ($factura) {
            $factura->canceled = 1;
            $factura->save();
        }

        $details = FactureDetail::where('facture_id', $factura->id)->get();
        $factureController = new FactureController();
        foreach ($details as $items) {
            $factureController->update_stock($items->id, 'delete');
        }
    }
}
