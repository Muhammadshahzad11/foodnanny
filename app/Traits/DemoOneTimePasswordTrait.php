<?php

namespace App\Traits;

use App\Enums\Activity;
use Dipokhalder\Settings\Facades\Settings;

trait DemoOneTimePasswordTrait
{
    public bool $required;
    public bool $demo;
    public int $verification;

    public function __construct()
    {
        $this->required     = false;
        $this->demo         = env('DEMO');
        $this->verification = Settings::group('site')->get('site_phone_verification');
        if ($this->verification == Activity::ENABLE && !$this->demo) {
            $this->required = true;
        }
    }
}
