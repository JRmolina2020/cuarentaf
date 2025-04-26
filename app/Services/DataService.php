<?php
// app/Services/FactureService.php

namespace App\Services;

use App\Models\Facture;
use App\Models\FactureDetail;
use Illuminate\Support\Facades\DB;

class DataService
{
    public function getFactureWithClientAndResolution($id)
    {
        // Lógica para obtener la factura con el cliente y resolución
        return Facture::join('clients', 'clients.id', '=', 'factures.client_id')
            ->where('factures.id', $id)
            ->select(
                'factures.id',
                'factures.created_at',
                'factures.date_facture',
                'factures.tot',
                'clients.phone',
                'clients.fullname',
                'clients.nit',
                'clients.dv',
                'clients.email'
            )
            ->first(); // Usar `first()` para obtener un único registro
    }
    public function getProductsItems($id)
    {
        // Obtén los detalles de los productos asociados a la factura
        $products = FactureDetail::join('products as p', 'p.id', '=', 'facture_details.product_id')
            ->join('factures as f', 'f.id', '=', 'facture_details.facture_id')
            ->select(
                'f.id as facture_id',
                'facture_details.id as detail_id',
                'p.name as product_name',
                'p.code as code',
                'facture_details.quantity',
                'facture_details.price',
                'facture_details.sub',
                'facture_details.disc', // Descuento de la línea
                'facture_details.tot'
            )
            ->where('f.id', $id)
            ->orderBy('p.name', 'ASC')
            ->get();

        // Transformar los productos al formato requerido
        $items = $products->map(function ($item) {
            return [
                "code_reference"    => $item->code,
                "name"              => $item->product_name ?? 'Producto sin nombre',
                "quantity"          => (int) ($item->quantity ?? 0),
                "discount_rate"     => (float) (0),
                "price"             => (float) ($item->price ?? 0),
                "tax_rate"          => 0, // Ajusta según tus necesidades
                "unit_measure_id"   => 70, // Ajusta según tus necesidades
                "standard_code_id"  => 1,  // Ajusta según tus necesidades
                "is_excluded"       => 1,  // Ajusta según tus necesidades
                "tribute_id"        => 1,  // Ajusta según tus necesidades
                "withholding_taxes" => [],
            ];
        })->toArray();

        return $items; // Devuelve el array transformado
    }
}
