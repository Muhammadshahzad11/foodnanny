<?php

namespace Database\Seeders;

use App\Models\FrontendItem;
use App\Models\ItemAttribute;
use App\Models\User;
use App\Traits\Seeder\ItemSeederTraitEight;
use App\Traits\Seeder\ItemSeederTraitEighteen;
use App\Traits\Seeder\ItemSeederTraitEleven;
use App\Traits\Seeder\ItemSeederTraitFifteen;
use App\Traits\Seeder\ItemSeederTraitFive;
use App\Traits\Seeder\ItemSeederTraitFour;
use App\Traits\Seeder\ItemSeederTraitFourteen;
use App\Traits\Seeder\ItemSeederTraitNine;
use App\Traits\Seeder\ItemSeederTraitNineteen;
use App\Traits\Seeder\ItemSeederTraitOne;
use App\Traits\Seeder\ItemSeederTraitSeven;
use App\Traits\Seeder\ItemSeederTraitSeventeen;
use App\Traits\Seeder\ItemSeederTraitSix;
use App\Traits\Seeder\ItemSeederTraitSixteen;
use App\Traits\Seeder\ItemSeederTraitTen;
use App\Traits\Seeder\ItemSeederTraitThirteen;
use App\Traits\Seeder\ItemSeederTraitThree;
use App\Traits\Seeder\ItemSeederTraitTwelve;
use App\Traits\Seeder\ItemSeederTraitTwenty;
use App\Traits\Seeder\ItemSeederTraitTwentyFive;
use App\Traits\Seeder\ItemSeederTraitTwentyFour;
use App\Traits\Seeder\ItemSeederTraitTwentyOne;
use App\Traits\Seeder\ItemSeederTraitTwentyThree;
use App\Traits\Seeder\ItemSeederTraitTwentyTwo;
use App\Traits\Seeder\ItemSeederTraitTwo;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Enums\Status;
use App\Models\ItemExtra;
use Illuminate\Support\Str;
use App\Models\ItemVariation;
use App\Models\Tax;
use Dipokhalder\EnvEditor\EnvEditor;


class ItemTableSeeder extends Seeder
{
    use ItemSeederTraitOne;
    use ItemSeederTraitTwo;
    use ItemSeederTraitThree;
    use ItemSeederTraitFour;
    use ItemSeederTraitFive;
    use ItemSeederTraitSix;
    use ItemSeederTraitSeven;
    use ItemSeederTraitEight;
    use ItemSeederTraitNine;
    use ItemSeederTraitTen;
    use ItemSeederTraitEleven;
    use ItemSeederTraitTwelve;
    use ItemSeederTraitThirteen;
    use ItemSeederTraitFourteen;
    use ItemSeederTraitFifteen;
    use ItemSeederTraitSixteen;
    use ItemSeederTraitSeventeen;
    use ItemSeederTraitEighteen;
    use ItemSeederTraitNineteen;
    use ItemSeederTraitTwenty;
    use ItemSeederTraitTwentyOne;
    use ItemSeederTraitTwentyTwo;
    use ItemSeederTraitTwentyThree;
    use ItemSeederTraitTwentyFour;
    use ItemSeederTraitTwentyFive;

    public array $itemAttributes;
    public array $itemTaxes;

    public function run(): void
    {
        $envService = new EnvEditor();
        if ($envService->getValue('DEMO')) {
            $attributes = ItemAttribute::all()->groupBy('restaurant_id');
            foreach ($attributes as $restaurant_id => $itemAttributes) {
                foreach ($itemAttributes as $attribute) {
                    $this->itemAttributes[$restaurant_id][] = $attribute->id;
                }
            }

            $taxesWithRestaurant = Tax::all()->groupBy('restaurant_id');
            foreach ($taxesWithRestaurant as $restaurant_id => $taxes) {
                foreach ($taxes as $tax) {
                    $this->itemTaxes[$restaurant_id][] = $tax->id;
                }
            }

            $this->seedItems();
        }
    }

    protected function seedItems(): void
    {
        $itemBoxes = array_merge(
            $this->itemSeederOne,
            $this->itemSeederTwo,
            $this->itemSeederThree,
            $this->itemSeederFour,
            $this->itemSeederFive,
            $this->itemSeederSix,
            $this->itemSeederSeven,
            $this->itemSeederEight,
            $this->itemSeederNine,
            $this->itemSeederTen,
            $this->itemSeederEleven,
            $this->itemSeederTwelve,
            $this->itemSeederThirteen,
            $this->itemSeederFourteen,
            $this->itemSeederFifteen,
            $this->itemSeederSixteen,
            $this->itemSeederSeventeen,
            $this->itemSeederEighteen,
            $this->itemSeederNineteen,
            $this->itemSeederTwenty,
            $this->itemSeederTwentyOne,
            $this->itemSeederTwentyTwo,
            $this->itemSeederTwentyThree,
            $this->itemSeederTwentyFour,
            $this->itemSeederTwentyFive
        );
        foreach ($itemBoxes as $itemBox) {
            $this->seedItemBox($itemBox);
        }
    }

    public function seedItemBox($itemBox): void
    {
        $itemObject = Item::create([
            'restaurant_id'             => $itemBox['item']['restaurant_id'],
            'name'                      => $itemBox['item']['name'],
            'slug'                      => Str::slug($itemBox['item']['name']) . uniqid(),
            'item_category_id'          => $itemBox['item']['item_category_id'],
            'price'                     => $itemBox['item']['price'],
            'is_halal'                  => $itemBox['item']['is_halal'],
            'available_time_start'      => $itemBox['item']['available_time_start'],
            'available_time_end'        => $itemBox['item']['available_time_end'],
            'discount_type'             => $itemBox['item']['discount_type'],
            'discount'                  => $itemBox['item']['discount'],
            'maximum_purchase_quantity' => $itemBox['item']['maximum_purchase_quantity'],
            'status'                    => Status::ACTIVE,
            'tax_id'                    => $this->itemTaxes[$itemBox['item']['restaurant_id']][$itemBox['item']['tax_index']],
            'item_type'                 => $itemBox['item']['item_type'],
            'order'                     => 1,
            'caution'                   => $itemBox['item']['caution'],
            'description'               => $itemBox['item']['description'],
            'creator_type'              => User::class,
            'creator_id'                => 1,
            'editor_type'               => User::class,
            'editor_id'                 => 1
        ]);
        if (file_exists(public_path('/images/seeder/item/' . strtolower(str_replace(' ', '_', $itemBox['item']['name'])) . '.png'))) {
            $itemObject->addMedia(public_path('/images/seeder/item/' . strtolower(str_replace(' ', '_', $itemBox['item']['name'])) . '.png'))->preservingOriginal()->toMediaCollection('item');
            $frontendItem = FrontendItem::find($itemObject->id);
            if ($frontendItem) {
                $frontendItem->addMedia(public_path('/images/seeder/item/' . strtolower(str_replace(' ', '_', $itemBox['item']['name'])) . '.png'))->preservingOriginal()->toMediaCollection('frontend-item');
            }
        }
        if (isset ($itemBox['variation'])) {
            foreach ($itemBox['variation'] as $variation) {
                ItemVariation::create([
                    'restaurant_id'     => $itemObject->restaurant_id,
                    'item_id'           => $itemObject->id,
                    'item_attribute_id' => $this->itemAttributes[$itemObject->restaurant_id][$variation['item_attribute_index']],
                    'name'              => $variation['name'],
                    'price'             => $variation['price'],
                    'status'            => Status::ACTIVE,
                    'creator_type'      => User::class,
                    'creator_id'        => 1,
                    'editor_type'       => User::class,
                    'editor_id'         => 1
                ]);
            }
        }
        if (isset ($itemBox['extra'])) {
            foreach ($itemBox['extra'] as $extra) {
                $extra['created_at'] = now();
                $extra['updated_at'] = now();
                ItemExtra::create([
                    'restaurant_id' => $itemObject->restaurant_id,
                    'item_id'       => $itemObject->id,
                    'name'          => $extra['name'],
                    'price'         => $extra['price'],
                    'status'        => $extra['status'],
                    'created_at'    => $extra['created_at'],
                    'updated_at'    => $extra['updated_at'],
                    'creator_type'  => User::class,
                    'creator_id'    => 1,
                    'editor_type'   => User::class,
                    'editor_id'     => 1
                ]);
            }
        }
    }
}
