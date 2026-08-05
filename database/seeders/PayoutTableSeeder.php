<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Payout;
use App\Models\Statement;
use App\Models\Restaurant;
use App\Enums\StatementType;
use App\Enums\StatementDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Dipokhalder\EnvEditor\EnvEditor;

class PayoutTableSeeder extends Seeder
{
    public function run(): void
    {
        $envService = new EnvEditor();

        if ($envService->getValue('DEMO')) {
            $payouts = [
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 500.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -500.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => User::class,
                        'model_id'     => 8,
                        'amount'       => 500.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => User::class,
                        'model_id'   => 8,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -500.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],

                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 2,
                        'amount'       => 7000.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 2,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -7000.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ], 
                [
                    'payout'    => [
                        'model_type'   => User::class,
                        'model_id'     => 13,
                        'amount'       => 500.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => User::class,
                        'model_id'   => 13,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -500.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => User::class,
                        'model_id'     => 8,
                        'amount'       => 600.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => User::class,
                        'model_id'   => 8,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -600.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],

                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 3,
                        'amount'       => 3000.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 3,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3000.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],

                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 4,
                        'amount'       => 2000.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 4,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -2000.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 5,
                        'amount'       => 3000.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 5,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3000.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ], 
                 [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 1700.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -1700.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => User::class,
                        'model_id'     => 8,
                        'amount'       => 1100.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => User::class,
                        'model_id'   => 8,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -1100.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => User::class,
                        'model_id'     => 13,
                        'amount'       => 300.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => User::class,
                        'model_id'   => 13,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -300.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 700.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -700.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 600.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -600.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 500.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -500.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 400.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -400.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3700.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3700.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3600.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3600.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3500.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3500.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3400.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3400.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3300.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3300.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
                [
                    'payout'    => [
                        'model_type'   => Restaurant::class,
                        'model_id'     => 1,
                        'amount'       => 3200.000000,
                        'date'         => Carbon::now(),
                        'creator_type' => User::class,
                        'creator_id'   => 1,
                        'editor_type'  => User::class,
                        'editor_id'    => 1,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now()
                    ],
                    'statement' => [
                        'model_type' => Restaurant::class,
                        'model_id'   => 1,
                        'date'       => Carbon::now(),
                        'order_id'   => NULL,
                        'type'       => StatementType::PAYOUT,
                        'detail'     => StatementDetail::RELEASE_PAYOUT,
                        'sign'       => '-',
                        'amount'     => -3200.000000,
                        'info'       => '[]',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                ],
            ];
            foreach ($payouts as $payout) {
                Payout::create([
                    'model_type'   => $payout['payout']['model_type'],
                    'model_id'     => $payout['payout']['model_id'],
                    'amount'       => $payout['payout']['amount'],
                    'date'         => $payout['payout']['date'],
                    'creator_type' => $payout['payout']['creator_type'],
                    'creator_id'   => $payout['payout']['creator_id'],
                    'editor_type'  => $payout['payout']['editor_type'],
                    'editor_id'    => $payout['payout']['editor_id'],
                    'created_at'   => $payout['payout']['created_at'],
                    'updated_at'   => $payout['payout']['updated_at']
                ]);
                Statement::create([
                    'model_type' => $payout['statement']['model_type'],
                    'model_id'   => $payout['statement']['model_id'],
                    'date'       => $payout['statement']['date'],
                    'order_id'   => $payout['statement']['order_id'],
                    'type'       => $payout['statement']['type'],
                    'detail'     => $payout['statement']['detail'],
                    'sign'       => $payout['statement']['sign'],
                    'amount'     => $payout['statement']['amount'],
                    'info'       => $payout['statement']['info'],
                    'created_at' => $payout['statement']['created_at'],
                    'updated_at' => $payout['statement']['updated_at'],
                ]);
            }
        }
    }
}
