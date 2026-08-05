<?php

namespace Database\Seeders;


use App\Enums\DisplayMode;
use App\Enums\Status;
use App\Models\Language;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Models\User;
use Illuminate\Database\Seeder;


class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        $english = [
            'name'         => 'English',
            'code'         => 'en',
            'display_mode' => DisplayMode::LTR,
            'status'       => Status::ACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $bangla = [
            'name'         => 'Bangla',
            'code'         => 'bn',
            'display_mode' => DisplayMode::LTR,
            'status'       => $envService->getValue('DEMO') ? Status::ACTIVE : Status::INACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $arabic = [
            'name'         => 'Arabic',
            'code'         => 'ar',
            'display_mode' => DisplayMode::RTL,
            'status'       => Status::ACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $french = [
            'name'         => 'French',
            'code'         => 'fr',
            'display_mode' => DisplayMode::LTR,
            'status'       => $envService->getValue('DEMO') ? Status::ACTIVE : Status::INACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $spanish = [
            'name'         => 'Spanish',
            'code'         => 'es',
            'display_mode' => DisplayMode::LTR,
            'status'       => $envService->getValue('DEMO') ? Status::ACTIVE : Status::INACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $german = [
            'name'         => 'German',
            'code'         => 'de',
            'display_mode' => DisplayMode::LTR,
            'status'       => $envService->getValue('DEMO') ? Status::ACTIVE : Status::INACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $portuguese = [
            'name'         => 'Portuguese',
            'code'         => 'pt',
            'display_mode' => DisplayMode::LTR,
            'status'       => $envService->getValue('DEMO') ? Status::ACTIVE : Status::INACTIVE,
            'creator_type' => User::class,
            'creator_id'   => 1,
            'editor_type'  => User::class,
            'editor_id'    => 1
        ];

        $englishLanguage = Language::create($english);
        if (file_exists(public_path('/images/seeder/language/english.png'))) {
            $englishLanguage->addMedia(public_path('/images/seeder/language/english.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $banglaLanguage = Language::create($bangla);
        if (file_exists(public_path('/images/seeder/language/bangla.png'))) {
            $banglaLanguage->addMedia(public_path('/images/seeder/language/bangla.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $arabicLanguage = Language::create($arabic);
        if (file_exists(public_path('/images/seeder/language/arabic.png'))) {
            $arabicLanguage->addMedia(public_path('/images/seeder/language/arabic.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $frenchLanguage = Language::create($french);
        if (file_exists(public_path('/images/seeder/language/french.png'))) {
            $frenchLanguage->addMedia(public_path('/images/seeder/language/french.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $spanishLanguage = Language::create($spanish);
        if (file_exists(public_path('/images/seeder/language/spanish.png'))) {
            $spanishLanguage->addMedia(public_path('/images/seeder/language/spanish.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $germanLanguage = Language::create($german);
        if (file_exists(public_path('/images/seeder/language/german.png'))) {
            $germanLanguage->addMedia(public_path('/images/seeder/language/german.png'))->preservingOriginal()->toMediaCollection('language');
        }

        $portugueseLanguage = Language::create($portuguese);
        if (file_exists(public_path('/images/seeder/language/portuguese.png'))) {
            $portugueseLanguage->addMedia(public_path('/images/seeder/language/portuguese.png'))->preservingOriginal()->toMediaCollection('language');
        }
    }
}
