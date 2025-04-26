<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FactusApiService
{
    protected $client;
    protected $apiUrl;
    protected $clientId;
    protected $clientSecret;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->client = new Client(); // ✅ Inicializa el cliente Guzzle
        $this->apiUrl = env('FACTUS_API_URL');
        $this->clientId = env('FACTUS_CLIENT_ID');
        $this->clientSecret = env('FACTUS_CLIENT_SECRET');
        $this->username = env('FACTUS_USERNAME');
        $this->password = env('FACTUS_PASSWORD');
    }

    /**
     * Obtiene el access_token de la API de Factus.
     */
    public function obtenerToken()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false, // Desactivar la verificación SSL
            ])->post($this->apiUrl . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $this->username,
                'password' => $this->password,
            ]);

            Log::info('Respuesta completa de Factus API: ' . $response->body());

            if ($response->failed()) {
                Log::error('Error al obtener el token: ' . $response->body());
                return null;
            }

            return $response->json()['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Excepción al obtener el token: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Envía una factura electrónica a la API de Factus.
     */
    public function sendInvoice($facturaData)
    {
        $token = $this->obtenerToken();
        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {
            $response = $this->client->post("{$this->apiUrl}/v1/bills/validate", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                ],
                'json' => $facturaData,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error al enviar la factura: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    public function getInvoices($identification)
    {
        $token = $this->obtenerToken(); // Obtiene el token de acceso
        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {
            // Realizar la solicitud GET a la API de Factus
            $response = $this->client->get("{$this->apiUrl}/v1/bills", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'filter[identification]' => $identification, // Filtro de identificación
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error al obtener las facturas: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Obtiene un rango de numeración por su ID desde la API de Factus.
     */
    /**
     * Obtiene los rangos de numeración con filtros desde la API de Factus.
     */
    public function getNumberingRanges(array $filters = [])
    {
        $token = $this->obtenerToken();
        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/v1/numbering-ranges", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                ],
                'query' => $filters,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error al obtener los rangos de numeración: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
