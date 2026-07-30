<?php

namespace Database\Seeders;

use App\Models\User;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;
use App\Models\ItemAddon;

class ItemAddonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itemsaddons = [
            // Restaurant 01
            [
                'restaurant_id'        => 1,
                'item_id'              => 44,
                'addon_item_id'        => 45,
                'addon_item_variation' => json_encode((object)['1' => 98]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 44,
                'addon_item_id'        => 46,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 43,
                'addon_item_id'        => 45,
                'addon_item_variation' => json_encode((object)['1' => 99]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 35,
                'addon_item_id'        => 45,
                'addon_item_variation' => json_encode((object)['1' => 98]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 33,
                'addon_item_id'        => 52,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 38,
                'addon_item_id'        => 50,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 36,
                'addon_item_id'        => 47,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 32,
                'addon_item_id'        => 45,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 30,
                'addon_item_id'        => 46,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 28,
                'addon_item_id'        => 50,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 26,
                'addon_item_id'        => 47,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 23,
                'addon_item_id'        => 46,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 22,
                'addon_item_id'        => 50,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 22,
                'addon_item_id'        => 51,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 20,
                'addon_item_id'        => 52,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 16,
                'addon_item_id'        => 47,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 12,
                'addon_item_id'        => 46,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 9,
                'addon_item_id'        => 45,
                'addon_item_variation' => json_encode((object)['1' => 99]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 7,
                'addon_item_id'        => 50,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 3,
                'addon_item_id'        => 48,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 2,
                'addon_item_id'        => 50,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 1,
                'item_id'              => 1,
                'addon_item_id'        => 46,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 02
            [
                'restaurant_id'        => 2,
                'item_id'              => 86,
                'addon_item_id'        => 89,
                'addon_item_variation' => json_encode((object)['1' => 168]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 86,
                'addon_item_id'        => 90,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 86,
                'addon_item_id'        => 91,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 84,
                'addon_item_id'        => 90,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 92,
                'addon_item_id'        => 90,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 73,
                'addon_item_id'        => 90,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 76,
                'addon_item_id'        => 87,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 82,
                'addon_item_id'        => 88,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 72,
                'addon_item_id'        => 88,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 70,
                'addon_item_id'        => 91,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 66,
                'addon_item_id'        => 92,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 62,
                'addon_item_id'        => 88,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 60,
                'addon_item_id'        => 88,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 2,
                'item_id'              => 59,
                'addon_item_id'        => 90,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 02
            [
                'restaurant_id'        => 3,
                'item_id'              => 130,
                'addon_item_id'        => 143,
                'addon_item_variation' => json_encode((object)['1' => 268]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 125,
                'addon_item_id'        => 142,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 126,
                'addon_item_id'        => 141,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 123,
                'addon_item_id'        => 136,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 121,
                'addon_item_id'        => 140,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 116,
                'addon_item_id'        => 135,
                'addon_item_variation' => json_encode((object)['1' => 263]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 104,
                'addon_item_id'        => 136,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 108,
                'addon_item_id'        => 137,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 110,
                'addon_item_id'        => 138,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 103,
                'addon_item_id'        => 138,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 101,
                'addon_item_id'        => 137,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 96,
                'addon_item_id'        => 138,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 3,
                'item_id'              => 93,
                'addon_item_id'        => 141,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 04
            [
                'restaurant_id'        => 4,
                'item_id'              => 166,
                'addon_item_id'        => 170,
                'addon_item_variation' => json_encode((object)['1' => 314]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 163,
                'addon_item_id'        => 173,
                'addon_item_variation' => json_encode((object)['1' => 320]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 160,
                'addon_item_id'        => 172,
                'addon_item_variation' => json_encode((object)['1' => 317]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 155,
                'addon_item_id'        => 171,
                'addon_item_variation' => json_encode((object)['1' => 316]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 153,
                'addon_item_id'        => 169,
                'addon_item_variation' => json_encode((object)['1' => 311]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 151,
                'addon_item_id'        => 170,
                'addon_item_variation' => json_encode((object)['1' => 314]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 149,
                'addon_item_id'        => 172,
                'addon_item_variation' => json_encode((object)['1' => 318]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 4,
                'item_id'              => 144,
                'addon_item_id'        => 171,
                'addon_item_variation' => json_encode((object)['1' => 316]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 05
            [
                'restaurant_id'        => 5,
                'item_id'              => 217,
                'addon_item_id'        => 223,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 213,
                'addon_item_id'        => 222,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 211,
                'addon_item_id'        => 221,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 207,
                'addon_item_id'        => 221,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 194,
                'addon_item_id'        => 217,
                'addon_item_variation' => json_encode((object)['1' => 422]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 198,
                'addon_item_id'        => 220,
                'addon_item_variation' => json_encode((object)['1' => 425]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 202,
                'addon_item_id'        => 218,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 184,
                'addon_item_id'        => 219,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 190,
                'addon_item_id'        => 221,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 193,
                'addon_item_id'        => 222,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 5,
                'item_id'              => 177,
                'addon_item_id'        => 220,
                'addon_item_variation' => json_encode((object)['1' => 424]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 06
            [
                'restaurant_id'        => 6,
                'item_id'              => 246,
                'addon_item_id'        => 254,
                'addon_item_variation' => json_encode((object)['1' => 473]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 6,
                'item_id'              => 244,
                'addon_item_id'        => 253,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 6,
                'item_id'              => 240,
                'addon_item_id'        => 252,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 6,
                'item_id'              => 236,
                'addon_item_id'        => 252,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 6,
                'item_id'              => 234,
                'addon_item_id'        => 247,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 6,
                'item_id'              => 224,
                'addon_item_id'        => 248,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 07
            [
                'restaurant_id'        => 7,
                'item_id'              => 283,
                'addon_item_id'        => 292,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 281,
                'addon_item_id'        => 291,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 277,
                'addon_item_id'        => 290,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 273,
                'addon_item_id'        => 288,
                'addon_item_variation' => json_encode((object)['1' => 548]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 271,
                'addon_item_id'        => 286,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 7,
                'item_id'              => 268,
                'addon_item_id'        => 290,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 263,
                'addon_item_id'        => 291,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 255,
                'addon_item_id'        => 285,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 259,
                'addon_item_id'        => 285,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 7,
                'item_id'              => 262,
                'addon_item_id'        => 288,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 08
            [
                'restaurant_id'        => 8,
                'item_id'              => 313,
                'addon_item_id'        => 323,
                'addon_item_variation' => json_encode((object)['1' => 604]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 8,
                'item_id'              => 309,
                'addon_item_id'        => 322,
                'addon_item_variation' => json_encode((object)['1' => 601]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 8,
                'item_id'              => 305,
                'addon_item_id'        => 321,
                'addon_item_variation' => json_encode((object)['1' => 599]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 8,
                'item_id'              => 303,
                'addon_item_id'        => 323,
                'addon_item_variation' => json_encode((object)['1' => 604]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 8,
                'item_id'              => 298,
                'addon_item_id'        => 319,
                'addon_item_variation' => json_encode((object)['1' => 595]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 8,
                'item_id'              => 294,
                'addon_item_id'        => 321,
                'addon_item_variation' => json_encode((object)['1' => 600]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],


            // Restaurant 09
            [
                'restaurant_id'        => 9,
                'item_id'              => 344,
                'addon_item_id'        => 353,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 337,
                'addon_item_id'        => 352,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 335,
                'addon_item_id'        => 351,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 334,
                'addon_item_id'        => 351,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 324,
                'addon_item_id'        => 349,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 332,
                'addon_item_id'        => 348,
                'addon_item_variation' => json_encode((object)['1' => 640]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 9,
                'item_id'              => 328,
                'addon_item_id'        => 348,
                'addon_item_variation' => json_encode((object)['1' => 640]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 10
            [
                'restaurant_id'        => 10,
                'item_id'              => 387,
                'addon_item_id'        => 396,
                'addon_item_variation' => json_encode((object)['1' => 730]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 385,
                'addon_item_id'        => 395,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 380,
                'addon_item_id'        => 394,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 375,
                'addon_item_id'        => 389,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 371,
                'addon_item_id'        => 394,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 366,
                'addon_item_id'        => 393,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 360,
                'addon_item_id'        => 394,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 356,
                'addon_item_id'        => 394,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 10,
                'item_id'              => 354,
                'addon_item_id'        => 391,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 11
            [
                'restaurant_id'        => 11,
                'item_id'              => 423,
                'addon_item_id'        => 430,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 11,
                'item_id'              => 420,
                'addon_item_id'        => 429,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 11,
                'item_id'              => 413,
                'addon_item_id'        => 428,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 11,
                'item_id'              => 410,
                'addon_item_id'        => 426,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 11,
                'item_id'              => 402,
                'addon_item_id'        => 425,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 11,
                'item_id'              => 399,
                'addon_item_id'        => 425,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 12
            [
                'restaurant_id'        => 12,
                'item_id'              => 445,
                'addon_item_id'        => 457,
                'addon_item_variation' => json_encode((object)['1' => 842]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 12,
                'item_id'              => 438,
                'addon_item_id'        => 455,
                'addon_item_variation' => json_encode((object)['1' => 838]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 12,
                'item_id'              => 437,
                'addon_item_id'        => 455,
                'addon_item_variation' => json_encode((object)['1' => 838]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 12,
                'item_id'              => 432,
                'addon_item_id'        => 454,
                'addon_item_variation' => json_encode((object)['1' => 837]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 13
            [
                'restaurant_id'        => 13,
                'item_id'              => 485,
                'addon_item_id'        => 493,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 483,
                'addon_item_id'        => 492,
                'addon_item_variation' => json_encode((object)['1' => 901]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 480,
                'addon_item_id'        => 491,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 474,
                'addon_item_id'        => 490,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 467,
                'addon_item_id'        => 492,
                'addon_item_variation' => json_encode((object)['1' => 901]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 472,
                'addon_item_id'        => 490,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 460,
                'addon_item_id'        => 488,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 13,
                'item_id'              => 463,
                'addon_item_id'        => 487,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 14
            [
                'restaurant_id'        => 14,
                'item_id'              => 510,
                'addon_item_id'        => 529,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 14,
                'item_id'              => 504,
                'addon_item_id'        => 526,
                'addon_item_variation' => json_encode((object)['1' => 946]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 14,
                'item_id'              => 500,
                'addon_item_id'        => 527,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 14,
                'item_id'              => 498,
                'addon_item_id'        => 525,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 14,
                'item_id'              => 497,
                'addon_item_id'        => 526,
                'addon_item_variation' => json_encode((object)['1' => 947]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 14,
                'item_id'              => 494,
                'addon_item_id'        => 528,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],


            // Restaurant 15
            [
                'restaurant_id'        => 15,
                'item_id'              => 548,
                'addon_item_id'        => 557,
                'addon_item_variation' => json_encode((object)['1' => 1010]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 15,
                'item_id'              => 547,
                'addon_item_id'        => 556,
                'addon_item_variation' => json_encode((object)['1' => 1007]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 15,
                'item_id'              => 540,
                'addon_item_id'        => 555,
                'addon_item_variation' => json_encode((object)['1' => 1006]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 15,
                'item_id'              => 537,
                'addon_item_id'        => 555,
                'addon_item_variation' => json_encode((object)['1' => 1002]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 15,
                'item_id'              => 531,
                'addon_item_id'        => 554,
                'addon_item_variation' => json_encode((object)['1' => 1004]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 16
            [
                'restaurant_id'        => 16,
                'item_id'              => 582,
                'addon_item_id'        => 595,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 16,
                'item_id'              => 579,
                'addon_item_id'        => 594,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 16,
                'item_id'              => 572,
                'addon_item_id'        => 592,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 16,
                'item_id'              => 568,
                'addon_item_id'        => 591,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 16,
                'item_id'              => 559,
                'addon_item_id'        => 590,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 16,
                'item_id'              => 565,
                'addon_item_id'        => 594,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 17
            [
                'restaurant_id'        => 17,
                'item_id'              => 609,
                'addon_item_id'        => 627,
                'addon_item_variation' => json_encode((object)['1' => 1107]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 17,
                'item_id'              => 606,
                'addon_item_id'        => 625,
                'addon_item_variation' => json_encode((object)['1' => 1105]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 17,
                'item_id'              => 600,
                'addon_item_id'        => 627,
                'addon_item_variation' => json_encode((object)['1' => 1107]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 17,
                'item_id'              => 598,
                'addon_item_id'        => 627,
                'addon_item_variation' => json_encode((object)['1' => 1107]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 18
            [
                'restaurant_id'        => 18,
                'item_id'              => 651,
                'addon_item_id'        => 659,
                'addon_item_variation' => json_encode((object)['1' => 1169]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 18,
                'item_id'              => 646,
                'addon_item_id'        => 657,
                'addon_item_variation' => json_encode((object)['1' => 1167]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 18,
                'item_id'              => 643,
                'addon_item_id'        => 658,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 18,
                'item_id'              => 641,
                'addon_item_id'        => 655,
                'addon_item_variation' => json_encode((object)['1' => 1165]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 18,
                'item_id'              => 639,
                'addon_item_id'        => 656,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 18,
                'item_id'              => 631,
                'addon_item_id'        => 659,
                'addon_item_variation' => json_encode((object)['1' => 1169]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 19
            [
                'restaurant_id'        => 19,
                'item_id'              => 673,
                'addon_item_id'        => 685,
                'addon_item_variation' => json_encode((object)['1' => 1205]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 19,
                'item_id'              => 670,
                'addon_item_id'        => 687,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 19,
                'item_id'              => 669,
                'addon_item_id'        => 686,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 19,
                'item_id'              => 664,
                'addon_item_id'        => 685,
                'addon_item_variation' => json_encode((object)['1' => 1205]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 19,
                'item_id'              => 662,
                'addon_item_id'        => 688,
                'addon_item_variation' => json_encode((object)['1' => 1208]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 20
            [
                'restaurant_id'        => 20,
                'item_id'              => 706,
                'addon_item_id'        => 715,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 20,
                'item_id'              => 705,
                'addon_item_id'        => 711,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 20,
                'item_id'              => 698,
                'addon_item_id'        => 712,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 20,
                'item_id'              => 695,
                'addon_item_id'        => 712,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 20,
                'item_id'              => 691,
                'addon_item_id'        => 711,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 21
            [
                'restaurant_id'        => 21,
                'item_id'              => 734,
                'addon_item_id'        => 740,
                'addon_item_variation' => json_encode((object)['1' => 1295]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 21,
                'item_id'              => 722,
                'addon_item_id'        => 740,
                'addon_item_variation' => json_encode((object)['1' => 1294]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 21,
                'item_id'              => 726,
                'addon_item_id'        => 739,
                'addon_item_variation' => json_encode((object)['1' => 1292]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 21,
                'item_id'              => 718,
                'addon_item_id'        => 739,
                'addon_item_variation' => json_encode((object)['1' => 1292]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 21,
                'item_id'              => 721,
                'addon_item_id'        => 737,
                'addon_item_variation' => json_encode((object)['1' => 1289]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 22
            [
                'restaurant_id'        => 22,
                'item_id'              => 761,
                'addon_item_id'        => 765,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 22,
                'item_id'              => 757,
                'addon_item_id'        => 766,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 22,
                'item_id'              => 747,
                'addon_item_id'        => 764,
                'addon_item_variation' => json_encode((object)['1' => 1338]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 22,
                'item_id'              => 752,
                'addon_item_id'        => 763,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 22,
                'item_id'              => 746,
                'addon_item_id'        => 763,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 22,
                'item_id'              => 744,
                'addon_item_id'        => 765,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 23
            [
                'restaurant_id'        => 23,
                'item_id'              => 789,
                'addon_item_id'        => 797,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 23,
                'item_id'              => 787,
                'addon_item_id'        => 790,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 23,
                'item_id'              => 778,
                'addon_item_id'        => 792,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            [
                'restaurant_id'        => 23,
                'item_id'              => 775,
                'addon_item_id'        => 796,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 23,
                'item_id'              => 768,
                'addon_item_id'        => 797,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],

            // Restaurant 24
            [
                'restaurant_id'        => 24,
                'item_id'              => 821,
                'addon_item_id'        => 827,
                'addon_item_variation' => json_encode((object)['1' => 1449]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 24,
                'item_id'              => 818,
                'addon_item_id'        => 826,
                'addon_item_variation' => json_encode((object)['1' => 1448]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 24,
                'item_id'              => 817,
                'addon_item_id'        => 825,
                'addon_item_variation' => json_encode((object)['1' => 1446]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 24,
                'item_id'              => 809,
                'addon_item_id'        => 823,
                'addon_item_variation' => json_encode((object)['1' => 1442]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 24,
                'item_id'              => 800,
                'addon_item_id'        => 824,
                'addon_item_variation' => json_encode((object)['1' => 1444]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 24,
                'item_id'              => 798,
                'addon_item_id'        => 822,
                'addon_item_variation' => json_encode((object)['1' => 1439]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            // Restaurant 25
            [
                'restaurant_id'        => 25,
                'item_id'              => 847,
                'addon_item_id'        => 853,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 25,
                'item_id'              => 849,
                'addon_item_id'        => 851,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 25,
                'item_id'              => 844,
                'addon_item_id'        => 849,
                'addon_item_variation' => json_encode((object)['1' => 1486]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 25,
                'item_id'              => 840,
                'addon_item_id'        => 851,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 25,
                'item_id'              => 828,
                'addon_item_id'        => 853,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
            [
                'restaurant_id'        => 25,
                'item_id'              => 833,
                'addon_item_id'        => 851,
                'addon_item_variation' => json_encode((object)[]),
                'created_at'           => now(),
                'updated_at'           => now()
            ],
        ];

        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            foreach ($itemsaddons as $itemsaddon) {
                ItemAddon::create([
                    'restaurant_id'        => $itemsaddon['restaurant_id'],
                    'item_id'              => $itemsaddon['item_id'],
                    'addon_item_id'        => $itemsaddon['addon_item_id'],
                    'addon_item_variation' => $itemsaddon['addon_item_variation'],
                    'creator_type'         => User::class,
                    'creator_id'           => 1,
                    'editor_type'          => User::class,
                    'editor_id'            => 1,
                    'created_at'           => $itemsaddon['created_at'],
                    'updated_at'           => $itemsaddon['updated_at'],
                ]);
            }
        }
    }
}
