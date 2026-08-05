<?php

namespace Database\Seeders;

use App\Enums\CampaignType;
use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Enums\Status;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CampaignTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $campaigns = [
                [
                    'title'        => 'Flat 20 persent Off on First Order',
                    'slug'         => Str::slug('Flat 20 persent Off on First Order'),
                    'description' => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants &amp; items. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'  => now(),
                    'end_date'    => Carbon::now()->addDays(365),
                    'start_time'  => '01:00:00',
                    'end_time'    => '22:00:00',
                    'type'        => CampaignType::PAID,
                    'amount'      => 200,
                    'status'      => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'        => 'Seasonal Combo Meal',
                    'slug'         => Str::slug('Seasonal Combo Meal'),
                    'description' => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'  => now(),
                    'end_date'    => Carbon::now()->addDays(365),
                    'start_time'  => '01:00:00',
                    'end_time'    => '22:00:00',
                    'type'        => CampaignType::PAID,
                    'amount'      => 500,
                    'status'      => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ],
                [
                    'title'        => '10 persent Off on All Asian Cuisine',
                    'slug'         => Str::slug('10 persent Off on All Asian Cuisine'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => CampaignType::PAID,
                    'amount'       => 500,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Cheezy Blast Offer',
                    'slug'         => Str::slug('Cheezy Blast Offer'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => CampaignType::PAID,
                    'amount'       => 500,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Big Salad Combo',
                    'slug'         => Str::slug('Big Salad Combo'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => CampaignType::PAID,
                    'amount'       => 500,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ], 
            ];

            foreach ($campaigns as $campaign) {
                $campaignObject = Campaign::create($campaign);
                if (file_exists(public_path('/images/seeder/campaign/' . strtolower(str_replace(' ', '_', $campaign['title'])) . '_thumb' . '.png'))) {
                    $campaignObject->addMedia(public_path('/images/seeder/campaign/' . strtolower(str_replace(' ', '_', $campaign['title'])) . '_thumb' . '.png'))->preservingOriginal()->toMediaCollection('campaign-thumb');
                }

                if (file_exists(public_path('/images/seeder/campaign/' . strtolower(str_replace(' ', '_', $campaign['title'])) . '_banner' . '.png'))) {
                    $campaignObject->addMedia(public_path('/images/seeder/campaign/' . strtolower(str_replace(' ', '_', $campaign['title'])) . '_banner' . '.png'))->preservingOriginal()->toMediaCollection('campaign-cover');
                }
            }
        }
    }
}
