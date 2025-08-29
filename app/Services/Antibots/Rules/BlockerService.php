<?php

namespace App\Services\Antibots\Rules;

use Illuminate\Support\Facades\File;

class BlockerService
{
    public function run()
    {
        $ip = $this->getIpAddress();
        $userAgent = request()->header('User-Agent');

        $blockedIps = $this->getBlockedList('blocked_ip.log');
        $blockedUAs = $this->getBlockedList('blocked_ua.log');

        if (in_array($ip, $blockedIps) || in_array($userAgent, $blockedUAs)) {
            abort(403, 'Forbidden');
        }
    }
    public function getBlockedList(string $filename): array
    {
        $path = storage_path("logs/blacklist/{$filename}");

        if (!File::exists($path)) {
            return []; // Si el archivo no existe, no hay bloqueos.
        }

        $lines = File::get($path);
        return array_filter(array_map('trim', explode("\n", $lines)));
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
