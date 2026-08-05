<?php

namespace Database\Seeders;

use App\Enums\Ask;
use App\Models\User;
use Carbon\Carbon;
use App\Enums\Status;
use App\Models\Offer;
use App\Enums\OfferType;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class OfferTableSeeder extends Seeder
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
            $offers = [
                [
                    'title'        => 'Festival Food Fiesta',
                    'slug'         => Str::slug('Festival Food Fiesta'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => OfferType::REGULAR,
                    'amount'       => 25,
                    'location'     => null,
                    'latitude'     => null,
                    'longitude'    => null,
                    'is_single'    => Ask::NO,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Happy Hour Deals',
                    'slug'         => Str::slug('Happy Hour Deals'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => OfferType::PREMIER,
                    'amount'       => 25,
                    'location'     => null,
                    'latitude'     => null,
                    'longitude'    => null,
                    'is_single'    => Ask::NO,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'New Restaurant Launch Boost',
                    'slug'         => Str::slug('New Restaurant Launch Boost'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => OfferType::REGULAR,
                    'amount'       => 10,
                    'location'     => null,
                    'latitude'     => null,
                    'longitude'    => null,
                    'is_single'    => Ask::NO,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
                [
                    'title'        => 'Weekend Grill Boost',
                    'slug'         => Str::slug('Weekend Grill Boost'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => OfferType::PREMIER,
                    'amount'       => 40,
                    'location'     => null,
                    'latitude'     => null,
                    'longitude'    => null,
                    'is_single'    => Ask::NO,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],  
                [
                    'title'        => 'Spicy Fiesta Campaign',
                    'slug'         => Str::slug('Spicy Fiesta Campaign'),
                    'description'  => '<p><strong>Terms &amp; conditions: </strong></p><p>1.Valid for selected restaurants. </p><p>2.Foodnanny reserves the right to change terms, conditions, and promotional periods without any advance notice.</p><p><br></p>',
                    'start_date'   => now(),
                    'end_date'     => Carbon::now()->addDays(365),
                    'start_time'   => '01:00:00',
                    'end_time'     => '22:00:00',
                    'type'         => OfferType::PREMIER,
                    'amount'       => 25,
                    'location'     => null,
                    'latitude'     => null,
                    'longitude'    => null,
                    'is_single'    => Ask::NO,
                    'status'       => Status::ACTIVE,
                    'creator_type' => User::class,
                    'creator_id'   => 1,
                    'editor_type'  => User::class,
                    'editor_id'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ],
            ];

            foreach ($offers as $offer) {
                $offerObject = Offer::create($offer);
                if (file_exists(public_path('/images/seeder/offer/' . strtolower(str_replace(' ', '_', $offer['title'])) . '_thumb' . '.png'))) {
                    $offerObject->addMedia(public_path('/images/seeder/offer/' . strtolower(str_replace(' ', '_', $offer['title'])) . '_thumb' . '.png'))->preservingOriginal()->toMediaCollection('offer-thumb');
                }

                if (file_exists(public_path('/images/seeder/offer/' . strtolower(str_replace(' ', '_', $offer['title'])) . '_banner' . '.png'))) {
                    $offerObject->addMedia(public_path('/images/seeder/offer/' . strtolower(str_replace(' ', '_', $offer['title'])) . '_banner' . '.png'))->preservingOriginal()->toMediaCollection('offer-cover');
                }
            };
        }
    }
}
