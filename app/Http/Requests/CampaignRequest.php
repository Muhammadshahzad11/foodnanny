<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use App\Enums\CampaignType;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'title'       => ['required', 'string', 'max:190'],
            'tag'         => ['nullable', 'string', 'max:80'],
            'amount'      => request('type') == CampaignType::FREE ? ['nullable', 'numeric'] : ['required', 'numeric', new IniAmount()],
            'description' => ['required', 'string', 'max:5000'],
            'start_date'  => ['required', 'string', 'max:190'],
            'end_date'    => ['required', 'string', 'max:190'],
            'start_time'  => ['required', 'string', 'max:190'],
            'end_time'    => ['required', 'string', 'max:190'],
            'type'        => ['required', 'numeric', 'max:24'],
            'thumbnail'   => $this->route('campaign.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cover'       => $this->route('campaign.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->isNotNull(request('start_date')) && strtotime(request('end_date')) < strtotime(request('start_date'))) {
                $validator->errors()->add('end_date', trans('all.message.end_date_cannot_older_than_start_date'));
            }
            if ($this->isNotNull(request('start_date')) && $this->checkToDate()) {
                $validator->errors()->add('end_date', trans('all.message.end_date'));
            }

            if ($this->isNotNull(request('start_time')) && strtotime(request('end_time')) < strtotime(request('start_time'))) {
                $validator->errors()->add('end_time', trans('all.message.end_time_cannot_older_than_start_time'));
            }
        });
    }

    private function checkToDate()
    {
        $today = strtotime(date('Y-m-d'));
        if (strtotime(request('end_date')) < $today) {
            return true;
        }
    }

    private function isNotNull($value): bool
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}
