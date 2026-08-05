<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PusherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'pusher_app_id'      => ['required', 'string', 'max:1000'],
            'pusher_app_key'     => ['required', 'string', 'max:1000'],
            'pusher_app_secret'  => ['required', 'string', 'max:1000'],
            'pusher_app_cluster' => ['required', 'string', 'max:1000']
        ];
    }
}
