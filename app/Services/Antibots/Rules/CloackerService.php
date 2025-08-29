<?php

namespace App\Services\Antibots\Rules;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloackerService
{
    public function run()
    {
        $config = config('antibots.config');

        if (!$config['comprobate_country']) {
            return;
        }

        $ip = $this->getIpAddress();
        $geoData = $this->geolocationIp($ip);

        $countryCode = $geoData['countryCode'] ?? null;


        if (!$this->isAllowedCountry($config['countries_allowed'], $countryCode)) {
            // Opcional: guardar registro local
            file_put_contents(
                storage_path('logs/antibots_blocked.log'),
                "$countryCode (Detectado) " . gmdate("H:i:s") . "\n",
                FILE_APPEND
            );

            // Redirección inmediata
            abort(redirect()->away($config['url']));
        }
    }
    public function geolocationIp(string $ip): array
    {
        $response = Http::get("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,asname,mobile,proxy,hosting,query");

        if ($response->failed()) {
            Log::warning("Falló la geolocalización de IP: $ip");
            return [];
        }

        return $response->json();
    }

    public function isAllowedCountry(array $allowedCountries, ?string $visitorCountry): bool
    {
        if (!$visitorCountry) {
            return false;
        }

        foreach ($allowedCountries as $country) {
            if (stripos($country, $visitorCountry) !== false) {
                return true;
            }
        }

        return false;
    }

    public function getIpAddress(): string
    {
        return request()->server('HTTP_CF_CONNECTING_IP')
            ?? request()->server('HTTP_CLIENT_IP')
            ?? request()->server('HTTP_X_FORWARDED_FOR')
            ?? request()->server('HTTP_X_FORWARDED')
            ?? request()->server('HTTP_FORWARDED_FOR')
            ?? request()->server('HTTP_FORWARDED')
            ?? request()->ip();
    }
}
