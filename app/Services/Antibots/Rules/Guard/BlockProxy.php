<?php

namespace App\Services\Antibots\Rules\Guard;

use Illuminate\Support\Facades\Http;

class BlockProxy
{
    public array $ignoreList = [
        '91.108.5.7',
        '205.210.31.19',
        '91.108.5.49',
        '40.113.118.83',
    ];

    public function run()
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        if (!$ip || $ip === '127.0.0.1') {
            return; // No hace nada si es localhost
        }

        $response = Http::withoutVerifying()->get("https://blackbox.ipinfo.app/lookup/{$ip}");

        if ($response->failed()) {
            return; // Opcional: puedes hacer log de error aquí si deseas
        }

        if (trim($response->body()) === 'Y') {
            if (in_array($ip, $this->ignoreList)) {
                return;
            }

            // Guardar en archivo bloqueados.log
            file_put_contents(
                storage_path('logs/resultados/bloqueados.log'),
                "BLOQUEADO POR IP || user-agent: $userAgent\nIP: $ip || " . gmdate("Y-n-d") . " ----> " . gmdate("H:i:s") . "\n\n",
                FILE_APPEND
            );

            // Registrar IP en total.txt (como en el original)
            file_put_contents(
                storage_path('logs/resultados/resultado_total.log'),
                "$ip (Detectado por Rango de IP)\n",
                FILE_APPEND
            );

            // Bloquear acceso
            abort(403, 'Forbidden');
        }
    }

}
