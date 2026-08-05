<?php

namespace Database\Seeders;
 
use App\Models\ThemeSetting;
use Illuminate\Database\Seeder;
use Dipokhalder\Settings\Facades\Settings;

class ThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Settings::group('theme')->set([
            'theme_logo'            => "",
            'theme_favicon_logo'    => "",
            'theme_footer_logo'     => "",
            'theme_primary_color'   => "#148A3C",
            'theme_secondary_color' => "#0A3D28",
        ]);

        if(file_exists(public_path('/images/seeder/theme/logo.png'))) {
            $themeLogo = ThemeSetting::where(['key' => 'theme_logo'])->first();
            $themeLogo->addMedia(public_path('/images/seeder/theme/logo.png'))->preservingOriginal()->toMediaCollection('theme-logo');
        }

        if(file_exists(public_path('/images/seeder/theme/favicon.png'))) {
            $themeFaviconLogo = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
            $themeFaviconLogo->addMedia(public_path('/images/seeder/theme/favicon.png'))->preservingOriginal()->toMediaCollection('theme-favicon-logo');
        }

        if(file_exists(public_path('/images/seeder/theme/footer-logo.png'))) {
            $themeFooterLogo = ThemeSetting::where(['key' => 'theme_footer_logo'])->first();
            $themeFooterLogo->addMedia(public_path('/images/seeder/theme/footer-logo.png'))->preservingOriginal()->toMediaCollection('theme-footer-logo');
        }
    }
}
