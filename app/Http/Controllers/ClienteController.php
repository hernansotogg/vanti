<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Cliente;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\MercadoPagoService;


class ClienteController extends Controller
{


    public function __construct() {}
    public function index()
    {
        return Inertia::render('Vanti/BuscarNumero');
    }


    public function saveUser(Request $request)
    {
        // Ver el contenido para pruebas
        //dd($request->all());
        //1021212

        $validated = $request->validate([
            'empresa' => ['required', 'string'],
            'referencia' => ['required', 'string'],
            'dispositivo' => ['required', 'string'],
        ]);

        //dd($validated);
        $dispositivo = $validated['dispositivo'];
        $empresa = $validated['empresa'];
        $referencia= $validated['referencia'];

        if ($referencia) {
            // Hacer la consulta a la API
            try {
                // Llamar al endpoint consulta
                $response = Http::timeout(30)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post('http://194.15.36.142:8312/factura', [
                        'project' => $empresa,
                        'reference' => $referencia
                    ]);

                $data = $response->json();

                Log::info('Respuesta de consulta: ' . json_encode($data));

                // Verificar si hay deuda pendiente por pagar
                $tieneDeuda = false;

                // Verificar si la respuesta es exitosa y tiene datos
                if (isset($data['data']['ResultCode']) && $data['data']['ResultCode'] == 1) {
                    $dataAuthorizer = $data['data']['DataAuthorizer'] ?? null;

                    if ($dataAuthorizer) {
                        // Verificar si hay un monto mayor a 0 y no está pagado
                        $amount = $dataAuthorizer['Amount'] ?? 0;
                        $paidOut = $dataAuthorizer['PaidOut'] ?? true;

                        // Tiene deuda si el monto es mayor a 0 y no está pagado
                        $tieneDeuda = ($amount > 0 && !$paidOut);
                    }
                }

                // Si no tiene deuda, retornar con error
                if (!$tieneDeuda) {
                    return back()->withErrors(['no_deuda' => 'Ocurrio un error inesperado. Intentalo de nuevo mas tarde']);
                }


                // Guardar datos de la API en sesión
                $request->session()->put('api_data', $data);
            } catch (\Exception $e) {
                Log::error('Error inesperado: ' . $e->getMessage());

                return back()->withErrors(['no_deuda' => 'Ocurrio un error inesperado. Intentalo de nuevo mas tarde']);
            }

            $clienteExistente = Cliente::where('referencia', $referencia)->first();

            if (!$clienteExistente) {
                $nuevoCliente = Cliente::create([
                    "referencia" => $referencia,
                    "ip" => $request->ip(),
                    "status" => "1",
                    "uuid" => Str::uuid(),
                    "empresa" => $empresa,
                    "dispositivo" => $dispositivo,
                ]);
                $id = $nuevoCliente->id;
            } else {
                $clienteExistente->update([
                    'ip' => $request->ip(),
                    'status' => "1",
                    "empresa" => $empresa,
                    "dispositivo" => $dispositivo,
                ]);
                $id = $clienteExistente->id;
            }

            // Sesión general
            $request->session()->put('referencia', $referencia);
            $request->session()->put('empresa', $empresa);
            $request->session()->put('dispositivo', $dispositivo);
            $request->session()->put('clienteid', $id);
            $request->session()->put('ip', $request->ip());
            $request->session()->put('step', 1);
            $request->session()->put('tiene_deuda', $tieneDeuda);
            return to_route('proceso.pago');
        } else {
            return back()->withErrors(['error' => 'Estamos presentando fallas técnicas. Por favor intenta más tarde.']);
        }
    }

    public function detail(Request $request)
    {
        // Obtener datos de la sesión
        $apiData = $request->session()->get('api_data');
        $referencia = $request->session()->get('referencia');
        $tieneDeuda = $request->session()->get('tiene_deuda');
        $empresa = $request->session()->get('empresa');

        return Inertia::render('Vanti/Detail', [
            'apiData' => $apiData,
            'referencia' => $referencia,
            'empresa' => $empresa,
            'tieneDeuda' => $tieneDeuda
        ]);
    }

    public function pago(Request $request)
    {
        Log::info('Iniciando proceso de pago');

        $validated = $request->validate([
            "total" => "required|numeric",
        ]);

        Log::info('Validación exitosa', $validated);

        // Guardar todos los datos validados en sesión por separado
        $request->session()->put('total', $validated['total']);

        Log::info('Validated: ' . json_encode($validated));
        Log::info('Datos guardados en sesión correctamente');

        // Obtener datos de la sesión
        $referencia = $request->session()->get('referencia');
        $clienteId = $request->session()->get('clienteid');

        Log::info('Datos de sesión obtenidos', [
            'referencia' => $referencia,
            'clienteId' => $clienteId
        ]);

        if (!$referencia || !$clienteId) {
            Log::error('Sesión inválida', [
                'referencia' => $referencia,
                'clienteId' => $clienteId
            ]);
            return back()->withErrors(['error' => 'Sesión expirada. Por favor inicie el proceso nuevamente.']);
        }

        try {
            // Crear instancia del servicio de MercadoPago
            $mercadoPagoService = new MercadoPagoService();

            // Crear la preferencia de pago
            $result = $mercadoPagoService->createPaymentPreference(
                $validated['total'],
                "Pago de servicios Vanti - Código: {$referencia}",
                $referencia
            );

            if ($result['success']) {
                // Guardar información del pago en sesión
                $request->session()->put('payment_preference_id', $result['preference_id']);
                $request->session()->put('payment_amount', $validated['total']);

                Log::info('Redirigiendo a MercadoPago', [
                    'preference_id' => $result['preference_id'],
                    'amount' => $validated['total'],
                    'referencia' => $referencia
                ]);

                // Redirigir a MercadoPago
                return Inertia::location($result['init_point']);
            } else {
                Log::error('Error al crear preferencia de pago', $result);
                return back()->withErrors(['error' => $result['error'] ?? 'Error al procesar el pago']);
            }
        } catch (\Exception $e) {
            Log::error('Error inesperado en pago', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Error interno del servidor. Por favor intente más tarde.']);
        }
    }

    /**
     * Manejar respuesta exitosa de MercadoPago
     */
    public function pagoSuccess(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');
        $externalReference = $request->get('external_reference');

        Log::info('Pago exitoso recibido', [
            'payment_id' => $paymentId,
            'status' => $status,
            'external_reference' => $externalReference
        ]);

        return Inertia::render('Vanti/PaymentSuccess', [
            'payment_id' => $paymentId,
            'status' => $status,
            'referencia' => $externalReference
        ]);
    }

    /**
     * Manejar respuesta fallida de MercadoPago
     */
    public function pagoFailure(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');
        $externalReference = $request->get('external_reference');

        Log::warning('Pago fallido recibido', [
            'payment_id' => $paymentId,
            'status' => $status,
            'external_reference' => $externalReference
        ]);

        return Inertia::render('Vanti/PaymentFailure', [
            'payment_id' => $paymentId,
            'status' => $status,
            'referencia' => $externalReference,
            'error' => 'El pago no pudo ser procesado. Por favor intente nuevamente.'
        ]);
    }

    /**
     * Manejar respuesta pendiente de MercadoPago
     */
    public function pagoPending(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');
        $externalReference = $request->get('external_reference');

        Log::info('Pago pendiente recibido', [
            'payment_id' => $paymentId,
            'status' => $status,
            'external_reference' => $externalReference
        ]);

        return Inertia::render('Vanti/PaymentPending', [
            'payment_id' => $paymentId,
            'status' => $status,
            'referencia' => $externalReference
        ]);
    }

    /**
     * Webhook para notificaciones de MercadoPago
     */
    public function pagoWebhook(Request $request)
    {
        $data = $request->all();

        Log::info('Webhook de MercadoPago recibido', $data);

        // Aquí puedes procesar las notificaciones de MercadoPago
        // Por ejemplo, actualizar el estado del pago en la base de datos

        return response()->json(['status' => 'ok'], 200);
    }
}
