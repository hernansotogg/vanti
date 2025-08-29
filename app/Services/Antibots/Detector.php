<?php

namespace App\Services\Antibots;

use Illuminate\Http\Request;
use App\Services\Antibots\Rules\CloackerService;
use App\Services\Antibots\Rules\GuardianService;
use App\Services\Antibots\Rules\BlockerService;
use App\Services\Antibots\Rules\MobileDetect;

class Detector
{
    public $cloaker;
    public $guardian;
    public $blocker;
    public $mobileDetect;

    public function __construct(
        CloackerService $cloaker,
        GuardianService $guardian,
        BlockerService $blocker,
         MobileDetect $mobileDetect,

    ) {
        $this->cloaker = $cloaker;
        $this->guardian = $guardian;
        $this->blocker = $blocker;
        $this->mobileDetect = $mobileDetect;
    }

    public function run()
    {
        $config = config('antibots.config');

        // cloacker country
        if ($config['comprobate_country']) {
            $this->cloaker->run();
        }

        // antibots
        if (!empty($config['EYEZ']) && $config['EYEZ'] == 1) {
            $this->guardian->run();
        }

        // blocker
        if (!empty($config['blocker']) && $config['blocker'] === true) {
            $this->blocker->run();
        }

        // mobile detect
        if (!empty($config['mobile_detect']) && $config['mobile_detect'] === true) {
            if($this->mobileDetect->isMobile()){
                //is mobile
            }else{
                abort(redirect()->away($config['url']));
            }
        }

        return null;
    }
}
