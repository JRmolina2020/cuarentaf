<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\FactusApiService;
use App\Services\DataService;
use App\Models\Facture;
use App\Models\TypeDocument;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;


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
        //$id = 42;
        $id = $request->input('id');
        $facture = $this->DataService->getFactureWithClientAndResolution($id);
        $items = $this->DataService->getProductsItems($id);
        $date = Carbon::parse($facture->date_facture); // Convierte la fecha en un objeto Carbon
        $newDate = $date->addDay(); // Suma un día a la fecha
        $newDateFormatted = $newDate->format('Y-m-d');

        // traer tipo de documento electronico 
        $code = TypeDocument::where('type', 'Elec')
            ->value('code');

        try {
            $invoiceData = [
                "numbering_range_id" =>  $code,
                "reference_code" => 'HKS' . $facture->id,
                "observation" => "",
                "payment_form" => "1",
                "payment_due_date" =>  $newDateFormatted,
                "payment_method_code" => "10",
                "send_email" => true,
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
                    "email" => 'providerjr23@gmail.com',
                    "phone" => "1234567890",
                    "legal_organization_id" => "2",
                    "tribute_id" => "21",
                    "identification_document_id" => "3",
                    "municipality_id" => 404
                ],
                "items" => $items
            ];

            //dd($invoiceData);
            $response = $this->factusService->sendInvoice($invoiceData);

            $response['data']['bill']['id'];
            if (isset($response['data']['bill']['cufe'])) {
                $this->updateCufe($response, $id);
            }
            return ($response);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateCufe($response, $id)
    {
        $cufe = $response['data']['bill']['cufe'];
        $bill =   $response['data']['bill']['id'];
        $validated = $response['data']['bill']['validated'];
        $number = $response['data']['bill']['number'];
        $factura = Facture::where('id', $id)->first();

        if ($factura) {
            $factura->cufe = $cufe;
            $factura->bill_id = $bill;
            $factura->validated_at = $validated;
            $factura->numberf = $number;
            $factura->numbering_range_id = 8;
            $factura->save();

            return response()->json(['message' => 'CUFE actualizado con éxito']);
        } else {
            return response()->json(['message' => 'Factura no encontrada'], 404);
        }
    }
    public function sendEmail($id)
    {

        $facture = $this->DataService->getFactureWithClientAndResolution($id);
        $items = $this->DataService->getProductsItems($id);

        if (!$facture) {
            return response()->json(['error' => 'Factura no encontrada en la base de datos'], 404);
        }

        try {
            // 3. Generamos el PDF "al vuelo" usando una vista Blade
            // Nota: Debes crear el archivo en resources/views/pdf/factura.blade.php
            $pdf = Pdf::loadView('pdf.facture', compact('facture', 'items'));
            return $pdf->download('factura.pdf');
            // // 4. Convertimos el contenido del PDF a Base64
            // $pdfBase64 = base64_encode($pdf->output());

            // $emailLimpio = trim($facture->email);

            // $response = $this->factusService->sendBillEmail(
            //     $facture->numberf,
            //     $emailLimpio, // Enviamos el correo sin espacios
            //     $pdfBase64
            // );

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Factura generada y enviada al cliente',
            //     'factus_response' => $response
            // ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            dd($responseBody);
        }
    }
}
