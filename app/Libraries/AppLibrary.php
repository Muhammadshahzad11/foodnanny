<?php

namespace App\Libraries;

use App\Enums\Ask;
use App\Enums\Availability;
use App\Enums\CurrencyPosition;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class AppLibrary
{
    public static function date($date, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT');
        }
        return Carbon::parse($date)->format($pattern);
    }

    public static function time($time, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT');
        }
        return Carbon::parse($time)->format($pattern);
    }

    public static function datetime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT') . ', ' . env('DATE_FORMAT');
        }
        return Carbon::parse($dateTime)->format($pattern);
    }

    public static function increaseDate($dateTime, $days, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('DATE_FORMAT');
        }
        return Carbon::parse($dateTime)->addDays($days)->format($pattern);
    }

    public static function deliveryTime($dateTime, $pattern = null): string
    {
        if (!$pattern) {
            $pattern = env('TIME_FORMAT');
        }
        $explode = explode('-', $dateTime);
        if (count($explode) == 2) {
            return Carbon::parse(trim($explode[0]))->format($pattern) . ' - ' . Carbon::parse(trim($explode[1]))->format($pattern);
        }
        return '';
    }

    public static function deliveryTimeCheck($dateTime, $pattern = null): string
    {
        if ($dateTime) {
            [$startTime, $endTime] = explode(' - ', $dateTime);
            $currentTime = new DateTime();

            $startTimeObj = DateTime::createFromFormat('H:i', $startTime);
            $endTimeObj = DateTime::createFromFormat('H:i', $endTime);

            if ($startTimeObj && $endTimeObj) {
                $slotDuration = Settings::group('order_setup')->get('order_setup_schedule_order_slot_duration') ?? 30;
                $thirtyMinutesBefore = (clone $startTimeObj)->sub(new DateInterval('PT' . $slotDuration . 'M'));

                if ($currentTime >= $thirtyMinutesBefore && $currentTime <= $endTimeObj) {
                    return "Now";
                } else {
                    if (!$pattern) {
                        $pattern = env('TIME_FORMAT', 'h:i A');
                    }
                    $explode = explode('-', $dateTime);
                    if (count($explode) == 2) {
                        return Carbon::parse(trim($explode[0]))->format($pattern) . ' - ' . Carbon::parse(trim($explode[1]))->format($pattern);
                    }
                    return '';
                }
            }
            return '';
        }
        return '';
    }

    public static function associativeToNumericArrayBuilder($array): array
    {
        $i          = 1;
        $buildArray = [];
        if (count($array)) {
            foreach ($array as $arr) {
                if (isset($arr['children'])) {
                    $children = $arr['children'];
                    unset($arr['children']);

                    $arr['parent']  = 0;
                    $buildArray[$i] = $arr;
                    $parentId       = $i;
                    $i++;
                    foreach ($children as $child) {
                        $child['parent'] = $parentId;
                        $buildArray[$i]  = $child;
                        $i++;
                    }
                } else {
                    $arr['parent']  = 0;
                    $buildArray[$i] = $arr;
                    $i++;
                }
            }
        }
        return $buildArray;
    }

    public static function numericToAssociativeArrayBuilder($array): array
    {
        $i          = 0;
        $buildArray = [];
        $indexById  = [];

        // Parents first, then attach children by real parent id (order-independent).
        if (count($array)) {
            foreach ($array as $arr) {
                if (!$arr['parent']) {
                    $buildArray[$i]         = $arr;
                    $indexById[$arr['id']]  = $i;
                    $i++;
                }
            }

            foreach ($array as $arr) {
                if ($arr['parent'] && isset($indexById[$arr['parent']])) {
                    $buildArray[$indexById[$arr['parent']]]['children'][] = $arr;
                }
            }
        }

        if ($buildArray) {
            foreach ($buildArray as $key => $build) {
                if ($build['url'] == "#" && !isset($build['children'])) {
                    unset($buildArray[$key]);
                }
            }
        }

        return $buildArray;
    }

    public static function permissionWithAccess(&$permissions, $rolePermissions)
    {
        if ($permissions) {
            foreach ($permissions as $permission) {
                if (isset($rolePermissions[$permission->id])) {
                    $permission->access = true;
                } else {
                    $permission->access = false;
                }
            }
        }
        return $permissions;
    }

    public static function menu(&$menus, $permissions): array
    {
        if ($menus && $permissions) {
            foreach ($menus as $key => $menu) {
                if ($menu['url'] == '#') {
                    continue;
                }

                // Hide items with no matching grant (avoids 403 from orphan menus).
                $perm = $permissions[$menu['url']] ?? null;
                $hasAccess = $perm && (
                    (is_array($perm) && !empty($perm['access']))
                    || (is_object($perm) && !empty($perm->access))
                );

                if (!$hasAccess) {
                    unset($menus[$key]);
                }
            }
        }
        return $menus;
    }

    public static function pluck($array, $value, $key = null, $type = 'object'): array
    {
        $returnArray = [];
        if ($array) {
            foreach ($array as $item) {
                if ($key != null) {
                    if ($type == 'array') {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item[$value];
                    } else {
                        $returnArray[$item[$key]] = strtolower($value) == 'obj' ? $item : $item->$value;
                    }
                } elseif ($value == 'obj') {
                    $returnArray[] = $item;
                } elseif ($type == 'array') {
                    $returnArray[] = $item[$value];
                } else {
                    $returnArray[] = $item->$value;
                }
            }
        }
        return $returnArray;
    }

    public static function username($name)
    {
        if ($name) {
            $username = strtolower(str_replace(' ', '', $name)) . rand(1, 999999);
            if (User::where(['username' => $username])->first()) {
                self::username($name);
            }
            return $username;
        }
    }

    public static function name($firstName, $lastName): string
    {
        return $firstName . ' ' . $lastName;
    }

    public static function amountCheck($amount, $attr = 'price'): object
    {
        $response = [
            'status'  => true,
            'message' => ''
        ];

        if (!is_numeric($amount)) {
            $response['status']  = false;
            $response['message'] = "This {$attr} must be integer.";
        }

        if ($amount <= 0) {
            if (!$response['status']) {
                return (object)$response;
            } else {
                $response['status']  = false;
                $response['message'] = "This {$attr} negative amount not allow.";
            }
        }

        $replaceValue = str_replace('.', '', $amount);
        if (strlen($replaceValue) > 12) {
            if (!$response['status']) {
                return (object)$response;
            } else {
                $response['status']  = false;
                $response['message'] = "This {$attr} length can't be greater than 12 digit.";
            }
        }

        if (!preg_match("/^\d{1,10}(\.\d{1,2})?$/", $amount)) {
            if (!$response['status']) {
                return (object)$response;
            } else {
                $response['status']  = false;
                $response['message'] = "This {$attr} amount provide invalid.";
            }
        }

        return (object)$response;
    }

    /** @var array{symbol: string, position: int, decimals: int, code: string}|null */
    protected static ?array $currencySettingsCache = null;

    /**
     * Runtime currency settings from DB (single source of truth for formatting).
     * Falls back to .env only when Settings are unavailable (e.g. early bootstrap).
     */
    public static function currencySettings(): array
    {
        if (self::$currencySettingsCache !== null) {
            return self::$currencySettingsCache;
        }

        $symbol   = null;
        $position = null;
        $decimals = null;

        try {
            // Use all() — after a partial set(), individual get() can return null for untouched keys.
            $site = Settings::group('site')->all();
            if (is_array($site) && $site !== []) {
                $symbol   = $site['site_default_currency_symbol'] ?? null;
                $position = $site['site_currency_position'] ?? null;
                $decimals = $site['site_digit_after_decimal_point'] ?? null;
            }
        } catch (\Throwable $e) {
            // fall through to env defaults below
        }

        self::$currencySettingsCache = [
            'symbol'   => (string) ($symbol !== null && $symbol !== '' ? $symbol : (env('CURRENCY_SYMBOL') ?: '')),
            'position' => (int) ($position !== null && $position !== '' ? $position : (env('CURRENCY_POSITION') ?: CurrencyPosition::LEFT)),
            'decimals' => max(0, (int) ($decimals !== null && $decimals !== '' ? $decimals : (env('CURRENCY_DECIMAL_POINT') ?: 2))),
            'code'     => self::currencyCodeFromSettings(),
        ];

        return self::$currencySettingsCache;
    }

    /**
     * Clear in-request currency cache after Site settings change.
     */
    public static function forgetCurrencySettingsCache(): void
    {
        self::$currencySettingsCache = null;
    }

    public static function currencyAmountFormat($amount): string
    {
        $settings  = self::currencySettings();
        $formatted = number_format((float) $amount, $settings['decimals'], '.', '');

        if ((int) $settings['position'] === CurrencyPosition::LEFT) {
            return $settings['symbol'] . $formatted;
        }

        return $formatted . $settings['symbol'];
    }

    public static function flatAmountFormat($amount): string
    {
        return number_format((float) $amount, self::currencySettings()['decimals'], '.', '');
    }

    public static function convertAmountFormat($amount): float
    {
        return (float) number_format((float) $amount, self::currencySettings()['decimals'], '.', '');
    }

    public static function currencyCode(): string
    {
        return self::currencySettings()['code'] ?: self::currencyCodeFromSettings();
    }

    protected static function currencyCodeFromSettings(): string
    {
        try {
            $currencyId = Settings::group('site')->get('site_default_currency');
            if ($currencyId) {
                $code = \App\Models\Currency::query()->where('id', $currencyId)->value('code');
                if ($code) {
                    return (string) $code;
                }
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return (string) (env('CURRENCY') ?: '');
    }

    public static function hexToRgb($hex, string $fallback = '0 0 0'): string
    {
        $hex = is_string($hex) ? ltrim(trim($hex), '#') : '';
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return $fallback;
        }
        return sprintf('%d %d %d', hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
    }

    public static function fcmDataBind($request): void
    {
        $cdn         = public_path("firebase-cdn.txt");
        $textContent = public_path("firebase-content.txt");
        $file        = public_path("firebase-messaging-sw.js");
        $content     = 'let config = {
        apiKey: "' . $request->notification_fcm_api_key . '",
        authDomain: "' . $request->notification_fcm_auth_domain . '",
        projectId: "' . $request->notification_fcm_project_id . '",
        storageBucket: "' . $request->notification_fcm_storage_bucket . '",
        messagingSenderId: "' . $request->notification_fcm_messaging_sender_id . '",
        appId: "' . $request->notification_fcm_app_id . '",
        measurementId: "' . $request->notification_fcm_measurement_id . '",' . "\n" . ' };' . "\n";
        File::put($file, File::get($cdn) . $content . File::get($textContent));
    }

    public static function defaultPermission($permissions)
    {
        $defaultPermission = (object)[];
        if (count($permissions)) {
            foreach ($permissions as $permission) {
                if ($permission->access) {
                    $defaultPermission = $permission;
                    break;
                }
            }
        }
        return $defaultPermission;
    }

    public static function domain($input): array|string|null
    {
        $input = trim($input, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);

        $link = '';
        if (isset($urlParts['port'])) {
            $link .= ':' . $urlParts['port'];
        }

        if (isset($urlParts['path'])) {
            $link .= $urlParts['path'];
        }

        return preg_replace('/^www\./', '', ($urlParts['host'] . $link));
    }

    public static function licenseApiResponse($response)
    {
        $header      = explode(';', $response->getHeader('Content-Type')[0]);
        $contentType = $header[0];
        if ($contentType == 'application/json') {
            $contents = $response->getBody()->getContents();
            $data     = json_decode($contents);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $data;
            }
            return $contents;
        }

        return ['status' => false, 'message' => 'data not found'];
    }


    public static function deleteDir($dirPath): void
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public static function cuisineString($arrays, bool $homeHighlightsOnly = false): string
    {
        $names = [];
        foreach ($arrays ?? [] as $array) {
            $cuisine = $array?->cuisine;
            if (blank($cuisine?->name)) {
                continue;
            }
            if ($homeHighlightsOnly && (int) ($cuisine->show_on_home ?? Ask::YES) !== Ask::YES) {
                continue;
            }
            $names[] = $cuisine->name;
        }

        return implode(', ', $names);
    }

    public static function timeSlots($timeSlots, $return = true): array|string
    {
        $string = '';
        $array  = [];
        $number = Carbon::now()->dayOfWeek;

        if (count($timeSlots) > 0) {
            $i = 0;
            foreach ($timeSlots as $timeSlot) {
                if ($timeSlot->day == $number) {
                    $array[$i] = [
                        'opening_time' => $timeSlot->opening_time,
                        'closing_time' => $timeSlot->closing_time
                    ];

                    if (!empty($string)) {
                        $string .= ', ';
                    }

                    $string .= self::time($timeSlot->opening_time) . ' - ' . self::time($timeSlot->closing_time);
                    $i++;
                }
            }
        }

        return $return ? $string : $array;
    }

    public static function availability($timeSlots): int
    {
        $status = Availability::CLOSE;
        if ($timeSlots != null) {
            $timeSlots = self::timeSlots($timeSlots, false);
            if (count($timeSlots) > 0) {
                foreach ($timeSlots as $timeSlot) {
                    if (strtotime($timeSlot['opening_time']) <= strtotime(date('H:i')) && strtotime($timeSlot['closing_time']) >= strtotime(date('H:i'))) {
                        $status = Availability::OPEN;
                        break;
                    }
                }
            }
        }
        return $status;
    }

    public static function timeWithRand(): string
    {
        return date('ymdhis') . rand(100, 999);
    }

    public static function htmlRemove($data): string
    {
        return rtrim(trim(strip_tags(str_replace('<p>', '', str_replace('</p>', ', ', $data)))), ',');
    }

    public static function isBetweenDate($start, $end): bool
    {
        if (empty($start) || empty($end)) {
            return false;
        }

        $startDate = Carbon::parse($start);
        $endDate   = Carbon::parse($end);

        return Carbon::now()->between($startDate, $endDate);
    }
}
