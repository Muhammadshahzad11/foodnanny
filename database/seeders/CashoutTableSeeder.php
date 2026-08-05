<?php

namespace Database\Seeders;

use App\Models\Cashout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class CashoutTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $cashouts = [
                [
                    'user_id'        => 1,
                    'amount'         => 10000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf',
                    'remarks'        => 'Paid 10,000 BDT from City Bank. Bank account: 123456789',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 14,
                    'amount'         => 15000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf2',
                    'remarks'        => 'Paid 15,000 BDT from Dutch Bangla Bank. Bank account: 987654321',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 15,
                    'amount'         => 10000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf3',
                    'remarks'        => 'Paid 10,000 BDT from BRAC Bank. Bank account: 1122334455',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ], 
                [
                    'user_id'        => 16,
                    'amount'         => 10000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 10,000 BDT from Eastern Bank Limited. Bank account: 5566778899',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 1,
                    'amount'         => 10000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 10,000 BDT from Eastern Bank Limited. Bank account: 5566778899',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 50,
                    'amount'         => 13000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 13,000 BDT from Eastern Bank Limited. Bank account: 5566778891',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 51,
                    'amount'         => 15000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 15,000 BDT from Eastern Bank Limited. Bank account: 5566778892',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 52,
                    'amount'         => 14000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 14,000 BDT from Eastern Bank Limited. Bank account: 5566778893',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 53,
                    'amount'         => 16000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 16,000 BDT from Eastern Bank Limited. Bank account: 5566778894',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 54,
                    'amount'         => 18000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf4',
                    'remarks'        => 'Paid 18,000 BDT from Eastern Bank Limited. Bank account: 5566778895',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 55,
                    'amount'         => 9000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf5',
                    'remarks'        => 'Paid 9,000 BDT from Eastern Bank Limited. Bank account: 5566778896',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 56,
                    'amount'         => 12000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf6',
                    'remarks'        => 'Paid 12,000 BDT from Eastern Bank Limited. Bank account: 5566778897',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
                [
                    'user_id'        => 57,
                    'amount'         => 14000.000000,
                    'date'           => Carbon::now(),
                    'transaction_id' => 'txn_3RR4JsGAN8SIuxA717fRwAnf7',
                    'remarks'        => 'Paid 14,000 BDT from Eastern Bank Limited. Bank account: 5566778898',
                    'creator_type'   => User::class,
                    'creator_id'     => 1,
                    'editor_type'    => User::class,
                    'editor_id'      => 1,
                    'created_at'     => Carbon::now(),
                    'updated_at'     => Carbon::now(),
                ],
            ];

            foreach ($cashouts as $cashout) {
                Cashout::create($cashout);
            }
        }
    }
}
