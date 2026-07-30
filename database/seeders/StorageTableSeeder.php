<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Enums\Activity;
use App\Enums\InputType;
use App\Models\GatewayOption;
use App\Models\Storage;
use Illuminate\Database\Seeder;

class StorageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public array $storages = [
         [
            "name"    => "Default",
            "slug"    => "local",
            "misc"    => null,
            "status"  => Activity::DISABLE,
        ],
        [
            "name"    => "AWS S3",
            "slug"    => "s3",
            "misc"    => null,
            "status"  => Activity::DISABLE,
            "options" => [
                [
                    "option"     => 'aws_key',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_secret',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_region',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_bucket',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_endpoint',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "true",
                        Activity::DISABLE => "false",
                    ],
                ],
                [
                    "option"     => 'aws_root',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_url',
                    "type"       => InputType::TEXT,
                    "activities" => '',
                ],
                [
                    "option"     => 'aws_status',
                    "value"      => Activity::DISABLE,
                    "type"       => InputType::SELECT,
                    "activities" => [
                        Activity::ENABLE  => "enable",
                        Activity::DISABLE => "disable",
                    ]
                ]
            ]
        ]
    ];

    public function run(): void
    {
        foreach ($this->storages as $storage) {
            $storageModel = Storage::create([
                'name'   => $storage['name'],
                'slug'   => $storage['slug'],
                'misc'   => json_encode($storage['misc']),
                'status' => Status::ACTIVE
            ]);
            if (isset($storage['options'])) {
                $this->gatewayOption($storageModel->id, $storage['options']);
            }
        }
    }

    public function gatewayOption($id, $options): void
    {
        foreach ($options as $option) {
            GatewayOption::create([
                'model_id'   => $id,
                'model_type' => 'App\Models\Storage',
                'option'     => $option['option'],
                'value'      => $option['value'] ?? "",
                'type'       => $option['type'],
                'activities' => json_encode($option['activities'])
            ]);
        }
    }
}
