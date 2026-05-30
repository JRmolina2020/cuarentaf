<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\ClientException;

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
        $this->client = new Client();
        $this->apiUrl = env('FACTUS_API_URL');
        $this->clientId = env('FACTUS_CLIENT_ID');
        $this->clientSecret = env('FACTUS_CLIENT_SECRET');
        $this->username = env('FACTUS_USERNAME');
        $this->password = env('FACTUS_PASSWORD');
    }


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

    public function sendNote($facturaData)
    {
        $token = $this->obtenerToken();

        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {

            $response = $this->client->post("{$this->apiUrl}/v1/credit-notes/validate", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                ],
                'json' => $facturaData,
            ]);

            return json_decode($response->getBody(), true);
        } catch (ClientException $e) {

            $response = $e->getResponse();
            $body = $response->getBody()->getContents();

            Log::error('Factus error: ' . $body);

            return json_decode($body, true);
        } catch (\Exception $e) {

            Log::error('Error general: ' . $e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }
    public function obtenerSuscripcionActual()
    {
        $token = $this->obtenerToken();
        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {
            $response = $this->client->get("{$this->apiUrl}/v1/subscriptions/current", [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error al obtener la suscripción: ' . $e->getMessage());
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
    public function sendBillEmail(string $number, string $email, string $pdfBase64)
    {
        $token = $this->obtenerToken();

        if (!$token) {
            return ['error' => 'No se pudo obtener el token'];
        }

        try {
            $url = "{$this->apiUrl}/v1/bills/send-email/{$number}";

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'email'               =>  $email,

                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error("Error al enviar email de la factura {$number}: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
