<?php

namespace App\Http\Storages\Requests;

use App\Enums\Activity;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Foundation\Http\FormRequest;

class S3 extends FormRequest
{
    public EnvEditor $envService;
    public bool $envUpdateStatus = true;

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if (request()->aws_status == Activity::ENABLE) {
            return [
                'aws_key'      => ['required', 'string'],
                'aws_secret'   => ['required', 'string'],
                'aws_region'   => ['required', 'string'],
                'aws_bucket'   => ['required', 'string'],
                'aws_endpoint' => ['nullable', 'numeric'],
                'aws_root'     => ['nullable', 'string'],
                'aws_url'      => ['nullable', 'string'],
                'aws_status'   => ['nullable', 'numeric']
            ];
        } else {
            return [
                'aws_key'      => ['nullable', 'string'],
                'aws_secret'   => ['nullable', 'string'],
                'aws_region'   => ['nullable', 'string'],
                'aws_bucket'   => ['nullable', 'string'],
                'aws_endpoint' => ['nullable', 'numeric'],
                'aws_root'     => ['nullable', 'string'],
                'aws_url'      => ['nullable', 'string'],
                'aws_status'   => ['nullable', 'numeric']
            ];
        }
    }


    public function envUpdate($request): void
    {
        $this->envService = new EnvEditor();
        $this->envService->addData([
            'AWS_ACCESS_KEY_ID'           => $request['aws_key'],
            'AWS_SECRET_ACCESS_KEY'       => $request['aws_secret'],
            'AWS_DEFAULT_REGION'          => $request['aws_region'],
            'AWS_BUCKET'                  => $request['aws_bucket'],
            'AWS_USE_PATH_STYLE_ENDPOINT' => $request['aws_endpoint'] == Activity::ENABLE ? 'true' : 'false',
            'AWS_ENDPOINT'                => '',
            'AWS_URL'                     => $request['aws_url'],
            'AWS_ROOT'                    => $request['aws_root']
        ]);
    }
}
