<?php

namespace App\Http\Controllers;


use App\Enums\Status;
use App\Models\Analytic;
use App\Models\ThemeSetting;
use App\Libraries\AppLibrary;

class RootController extends Controller
{

    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        $analytics      = Analytic::with('analyticSections')->where(['status' => Status::ACTIVE])->get();
        $themeFavicon   = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
        $favIcon        = $themeFavicon->faviconLogo;
        $primaryRow     = ThemeSetting::where(['group' => 'theme', 'key' => 'theme_primary_color'])->first();
        $secondaryRow   = ThemeSetting::where(['group' => 'theme', 'key' => 'theme_secondary_color'])->first();
        $primaryColor   = AppLibrary::hexToRgb(optional($primaryRow)->color, '243 104 5');
        $secondaryColor = AppLibrary::hexToRgb(optional($secondaryRow)->color, '31 31 57');
        return view('master', [
            'analytics'      => $analytics,
            'favicon'        => $favIcon,
            'primaryColor'   => $primaryColor,
            'secondaryColor' => $secondaryColor,
        ]);
    }
}
