<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura {{ $facture->numberf }}</title>
    <style>
        /* Configuraciones Base para PDF */
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #333;
            margin: 0px;
            padding: 0px;
        }

        /* Paleta de Colores Corporativa */
        :root {
            --primary-color: #2c3e50;
            /* Azul Grisáceo Oscuro */
            --secondary-color: #34495e;
            --accent-color: #3498db;
            /* Azul Brillante */
            --bg-light: #f4f7f6;
            --text-muted: #7f8c8d;
        }

        /* Contenedor Principal */
        .invoice-container {
            padding: 40px;
        }

        /* ============================
           ENCAZADO Y LOGO
           ============================ */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .header-logo {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .header-logo img {
            max-width: 180px;
            height: auto;
        }

        /* Ajusta el ancho de tu logo */

        .header-info {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .invoice-number {
            font-size: 16px;
            color: #3498db;
            font-weight: bold;
        }

        .invoice-date {
            color: #7f8c8d;
            font-size: 12px;
        }

        /* ============================
           SECCIÓN DE DATOS (EMISOR/RECEPTOR)
           ============================ */
        .data-section {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }

        .data-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .panel {
            padding: 15px;
            border-radius: 4px;
        }

        .panel-emitter {
            background-color: #2c3e50;
            color: white;
            margin-right: 10px;
        }

        .panel-client {
            background-color: #f4f7f6;
            color: #333;
            margin-left: 10px;
            border: 1px solid #e1e8ed;
        }

        .panel-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            color: #7f8c8d;
        }

        .panel-emitter .panel-title {
            color: #bdc3c7;
        }

        /* Título más claro en fondo oscuro */

        .company-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-details,
        .client-details {
            font-size: 12px;
            color: inherit;
        }

        /* ============================
           TABLA DE ÍTEMS ESTILIZADA
           ============================ */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed;
        }

        .items-table thead th {
            background-color: #34495e;
            color: white;
            text-align: left;
            padding: 12px 10px;
            font-size: 12px;
            text-transform: uppercase;
            border: none;
        }

        /* Alineaciones de columna */
        .col-desc {
            width: 55%;
        }

        .col-cant {
            width: 10%;
            text-align: center;
        }

        .col-price {
            width: 17%;
            text-align: right;
        }

        .col-sub {
            width: 18%;
            text-align: right;
        }

        .items-table tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #e1e8ed;
            vertical-align: top;
        }

        /* Cebra para filas */
        .items-table tbody tr:nth-child(even) td {
            background-color: #fbfcfc;
        }

        .item-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .item-desc {
            font-size: 11px;
            color: #7f8c8d;
        }

        /* ============================
           SECCIÓN DE TOTALES
           ============================ */
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .notes-column {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 30px;
            color: #7f8c8d;
            font-size: 11px;
        }

        .totals-column {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            float: right;
        }

        .totals-table td {
            padding: 8px 10px;
            text-align: right;
            border-bottom: 1px solid #e1e8ed;
        }

        .totals-label {
            font-weight: bold;
            color: #34495e;
            width: 60%;
        }

        .totals-value {
            width: 40%;
            color: #2c3e50;
        }

        /* Fila del Gran Total */
        .total-grand-row td {
            background-color: #2c3e50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            padding: 15px 10px;
        }

        .total-grand-row .totals-label {
            color: white;
        }

        .total-grand-row .totals-value {
            color: #3498db;
        }

        /* Acento de color en el total */

        /* ============================
           PIE DE PÁGINA
           ============================ */
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            padding: 20px 0;
            border-top: 1px solid #e1e8ed;
            background-color: #fbfcfc;
        }
    </style>
</head>

<body>

    <div class="invoice-container">


        <div class="header">
            <div class="header-logo">

            </div>
            <div class="header-info">
                <div class="invoice-title">Factura de Venta</div>
                <div class="invoice-number">N° {{ $facture->numberf }}</div>
                <div class="invoice-date">Fecha de Emisión: {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- DATOS EMISOR Y RECEPTOR --}}
        <div class="data-section">


            {{-- Columna Receptor (Datos del Cliente) --}}
            <div class="data-column">
                <div class="panel panel-client">
                    <div class="panel-title">CLIENTE</div>
                    <div class="company-name">{{ $facture->fullname }}</div>
                    <div class="client-details">
                        CC/NIT: {{ $facture->nit ?? 'N/A' }}<br>
                        {{ $facture->address ?? 'Dirección no registrada' }}<br>
                        {{ $facture->email ?? 'Email no registrado' }}<br>
                        {{ $facture->phone ?? 'Teléfono no registrado' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLA DE ÍTEMS --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Descripción</th>
                    <th class="col-cant">Cant.</th>
                    <th class="col-price">Precio Unit.</th>
                    <th class="col-sub">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp
                @foreach($items as $item)
                @php
                // Asegúrate de que quantity y price sean numéricos
                $qty = (float) $item['quantity'];
                $price = (float) $item['price'];
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;
                @endphp
                <tr>
                    <td>
                        <div class="item-name">{{ $item['name'] }}</div>
                        {{-- Opcional: Descripción corta si la tienes en tus datos --}}
                        {{-- <div class="item-desc">{{ $item['description'] ?? '' }}
    </div> --}}
    </td>
    <td class="col-cant">{{ number_format($qty, 0) }}</td>
    <td class="col-price">${{ number_format($price, 2) }}</td>
    <td class="col-sub">${{ number_format($subtotal, 2) }}</td>
    </tr>
    @endforeach
    </tbody>
    </table>

    {{-- SECCIÓN DE TOTALES Y NOTAS --}}
    <div class="totals-section">

        <div class="totals-column">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal</td>
                    <td class="totals-value">${{ number_format($totalAmount, 2) }}</td>
                </tr>
                {{-- Ejemplo de IVA (ajusta según tus datos reales) --}}
                {{-- @php $iva = $totalAmount * 0.19; @endphp
                    <tr>
                        <td class="totals-label">IVA (19%)</td>
                        <td class="totals-value">${{ number_format($iva, 2) }}</td>
                </tr> --}}
                <tr class="total-grand-row">
                    <td class="totals-label">TOTAL A PAGAR</td>
                    <td class="totals-value">${{ number_format($totalAmount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    </div>

    {{-- PIE DE PÁGINA FIJO --}}
    <div class="footer">
        TU EMPRESA S.A.S. | NIT: 900.123.456-7 | www.tuempresa.com<br>
        Generado automáticamente por el Sistema de Facturación Electrónica.
    </div>

</body>

</html>