<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private $client;

    public function __construct()
    {
        // Configurar MercadoPago con el access token
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $this->client = new PreferenceClient();
    }

    /**
     * Crear una preferencia de pago en MercadoPago
     *
     * @param float $amount Monto total a pagar
     * @param string $description Descripción del pago
     * @param string $externalReference Referencia externa (código del cliente)
     * @return array|null
     */
    public function createPaymentPreference($amount, $description = 'Pago de servicios vanti', $externalReference = null)
    {
        try {
            $preference = [
                "items" => [
                    [
                        "title" => $description,
                        "quantity" => 1,
                        "unit_price" => (float) $amount,
                        "currency_id" => "COP" // Peso colombiano
                    ]
                ],
                "back_urls" => [
                    "success" => $this->getWebhookBaseUrl() . '/pago/success',
                    "failure" => $this->getWebhookBaseUrl() . '/pago/failure',
                    "pending" => $this->getWebhookBaseUrl() . '/pago/pending'
                ],
                "auto_return" => "approved",
                "payment_methods" => [
                    "excluded_payment_methods" => [],
                    "excluded_payment_types" => [],
                    "installments" => 12
                ],
                "notification_url" => $this->getWebhookBaseUrl() . '/pago/webhook',
                "statement_descriptor" => "vanti SERVICIOS"
            ];

            // Agregar referencia externa si se proporciona
            if ($externalReference) {
                $preference["external_reference"] = $externalReference;
            }

            Log::info('Creando preferencia de MercadoPago', [
                'amount' => $amount,
                'description' => $description,
                'external_reference' => $externalReference
            ]);

            $result = $this->client->create($preference);

            Log::info('Preferencia creada exitosamente', [
                'preference_id' => $result->id,
                'init_point' => $result->init_point
            ]);

            return [
                'success' => true,
                'preference_id' => $result->id,
                'init_point' => $result->init_point,
                'sandbox_init_point' => $result->sandbox_init_point
            ];

        } catch (MPApiException $e) {
            Log::error('Error de API de MercadoPago', [
                'message' => $e->getMessage(),
                'status_code' => $e->getApiResponse()->getStatusCode(),
                'content' => $e->getApiResponse()->getContent()
            ]);

            return [
                'success' => false,
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ];

        } catch (\Exception $e) {
            Log::error('Error general en MercadoPago', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Error interno del servidor'
            ];
        }
    }

    /**
     * Obtener información de una preferencia
     *
     * @param string $preferenceId
     * @return array|null
     */
    public function getPreference($preferenceId)
    {
        try {
            $preference = $this->client->get($preferenceId);
            return [
                'success' => true,
                'preference' => $preference
            ];
        } catch (MPApiException $e) {
            Log::error('Error al obtener preferencia', [
                'preference_id' => $preferenceId,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener la URL base para webhooks
     *
     * @return string
     */
    private function getWebhookBaseUrl()
    {
        $webhookUrl = config('services.mercadopago.webhook_url');

        // Si no está configurada, usar APP_URL
        if (!$webhookUrl || $webhookUrl === 'http://localhost') {
            $webhookUrl = config('app.url');
        }

        return rtrim($webhookUrl, '/');
    }
}
