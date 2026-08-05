<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use App\Traits\DemoOneTimePasswordTrait;
use Carbon\Carbon;
use Dipokhalder\Settings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RestaurantInfoRequest extends FormRequest
{
    use DemoOneTimePasswordTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $cast = [];
        foreach (['restaurant_latitude', 'restaurant_longitude', 'restaurant_phone', 'restaurant_zip_code', 'owner_phone'] as $field) {
            if ($this->has($field) && $this->input($field) !== null && $this->input($field) !== '') {
                $cast[$field] = (string) $this->input($field);
            }
        }
        if ($cast) {
            $this->merge($cast);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'owner_name'              => ['required', 'string', 'max:255'],
            'owner_email'             => ['required', 'string', 'email', 'max:255', Rule::unique("users", "email")->whereNull('deleted_at')->where('is_guest', Ask::NO)],
            'owner_phone'             => ['required', 'string', Rule::unique("users", "phone")->whereNull('deleted_at')->where('country_code', request('owner_country_code'))->where('is_guest', Ask::NO)],
            'owner_country_code'      => ['required', 'string', 'max:10'],
            'owner_password'          => ['required', 'string', 'min:6'],
            'restaurant_name'         => ['required', 'string', 'max:190', Rule::unique("restaurants", "name")],
            'restaurant_email'        => ['required', 'email', 'max:190'],
            'restaurant_country_code' => ['required', 'string', 'max:10'],
            'restaurant_phone'        => ['required', 'string', 'max:20'],
            'restaurant_address'      => ['required', 'string', 'max:500'],
            'restaurant_city'         => ['required', 'string', 'max:190'],
            'restaurant_state'        => ['required', 'string', 'max:190'],
            'restaurant_zip_code'     => ['required', 'string', 'max:30'],
            'restaurant_latitude'     => ['nullable', 'string', 'max:190'],
            'restaurant_longitude'    => ['nullable', 'string', 'max:190'],
            'token'                   => $this->required ? ['required', 'string', 'max:100'] : ['nullable', 'string', 'max:100'],
            'terms_and_conditions'    => request('page_id') > 0 ? ['required', 'numeric'] : ['nullable', 'numeric'],
        ];
    }

    public function withValidator($validator): void
    {
        if ($this->required) {
            $validator->after(function ($validator) {
                $otp = DB::table('one_time_passwords')->where('code', request('owner_country_code'))->where('provider', request('owner_phone'))->where('token', request('token'));
                if ($otp->exists()) {
                    $difference = (int) Carbon::now()->diffInSeconds($otp->first()->created_at,true);
                    if ($difference > (int)Settings::group('otp')->get('otp_expire_time') * 60) {
                        $validator->errors()->add('token', trans('all.message.token_is_expired'));
                    } else {
                        if (!$validator->failed()) {
                            $otp->delete();
                        }
                    }
                } else {
                    $validator->errors()->add('token', trans('all.message.token_is_invalid'));
                }
            });
        }
    }
}
