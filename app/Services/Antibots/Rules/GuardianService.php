<?php

namespace App\Services\Antibots\Rules;

Use  App\Services\Antibots\Rules\Guard\BlockBots;
Use  App\Services\Antibots\Rules\Guard\BlockFp;
Use  App\Services\Antibots\Rules\Guard\BlockHn;
Use  App\Services\Antibots\Rules\Guard\BlockIsp;
Use  App\Services\Antibots\Rules\Guard\BlockProxy;
Use  App\Services\Antibots\Rules\Guard\BlockUa;


class GuardianService
{
    public BlockUa $ua;
    public BlockHn $hn;
    public BlockIsp $isp;
    public BlockFp $fp;
    public BlockProxy $proxy;
    public BlockBots $bots;

    public function __construct(
        BlockUa $ua,
        BlockHn $hn,
        BlockIsp $isp,
        BlockFp $fp,
        BlockProxy $proxy,
        BlockBots $bots
    ) {
        $this->ua = $ua;
        $this->hn = $hn;
        $this->isp = $isp;
        $this->fp = $fp;
        $this->proxy = $proxy;
        $this->bots = $bots;
    }
  public function run()
    {
        $config = config('antibots.config');

        //Antibots
        if (!empty($config['anti_bots']) && $config ['anti_bots'] === 'on') {
            $this->bots->run();
        }

        //User-agent blocker
        if (!empty($config['anti_ua']) && $config['anti_ua'] === 'on') {
            $this->ua->run();
        }

        // Hostname blocker
        if (!empty($config['anti_hn']) && $config['anti_hn'] === 'on') {
            $this->hn->run();
        }

        // ISP blocker
        if (!empty($config['anti_isp']) && $config['anti_isp'] === 'on') {
            $this->isp->run();
        }

        // Fingerprints blocker
        if (!empty($config['anti_fingerprints']) && $config['anti_fingerprints'] === 'on') {
            $this->fp->run();
        }

        // Proxy/VPN blocker in dev
        if (!empty($config['anti_proxy']) && $config['anti_proxy'] === 'on') {
            $this->proxy->run();
        }
    }
}
