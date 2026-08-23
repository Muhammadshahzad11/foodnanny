<?php

namespace App\Support;

use App\Enums\Ask;
use App\Enums\Role as EnumRole;
use App\Enums\Status;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Play Store demo customer: India dummy number, OTP skipped for this account only.
 */
class DemoCustomerLogin
{
    public const NAME = 'Play Store Demo';
    public const EMAIL = 'playstore.demo@customer.local';
    public const USERNAME = 'playstore-demo-customer';
    public const COUNTRY_CODE = '+91';
    public const PHONE = '9999999999';
    public const OTP = '123456';

    public static function matches(?string $code, ?string $phone): bool
    {
        $phoneDigits = preg_replace('/\D+/', '', (string) $phone);
        if ($phoneDigits === '') {
            return false;
        }

        return substr($phoneDigits, -10) === self::phoneDigits();
    }

    public static function phoneDigits(): string
    {
        $raw = preg_replace('/\D+/', '', (string) env('DEMO_CUSTOMER_PHONE', self::PHONE));

        return $raw !== '' ? substr($raw, -10) : self::PHONE;
    }

    public static function otp(): string
    {
        $otp = trim((string) env('DEMO_CUSTOMER_OTP', self::OTP));

        return $otp !== '' ? $otp : self::OTP;
    }

    public static function acceptsToken(?string $token): bool
    {
        $given = preg_replace('/\D+/', '', (string) $token);
        $fixed = preg_replace('/\D+/', '', self::otp());

        return $given === $fixed || in_array($given, ['0000', '000000', '1234'], true);
    }

    public static function findUser(?string $code, ?string $phone): ?User
    {
        if (!self::matches($code, $phone)) {
            return null;
        }

        $exact = User::query()
            ->where('country_code', $code)
            ->where('phone', $phone)
            ->first();
        if ($exact) {
            return $exact;
        }

        return User::query()
            ->where('phone', self::phoneDigits())
            ->first();
    }

    public static function ensureUser(): User
    {
        $user = User::query()
            ->where('phone', self::phoneDigits())
            ->where(function ($query) {
                $query->where('country_code', self::COUNTRY_CODE)
                    ->orWhere('country_code', '91')
                    ->orWhere('country_code', '+91');
            })
            ->first();

        if ($user) {
            if ((int) $user->status !== Status::ACTIVE) {
                $user->status = Status::ACTIVE;
                $user->save();
            }
            if (!$user->hasRole(EnumRole::CUSTOMER)) {
                $user->assignRole(EnumRole::CUSTOMER);
            }

            return $user;
        }

        $user = User::create([
            'name'                 => self::NAME,
            'email'                => self::EMAIL,
            'phone'                => self::phoneDigits(),
            'username'             => self::USERNAME,
            'email_verified_at'    => now(),
            'password'             => Hash::make(self::otp()),
            'restaurant_id'        => 0,
            'status'               => Status::ACTIVE,
            'country_code'         => self::COUNTRY_CODE,
            'is_guest'             => Ask::NO,
            'balance'              => 0,
            'terms_and_conditions' => Ask::YES,
        ]);
        $user->assignRole(EnumRole::CUSTOMER);

        return $user;
    }
}
