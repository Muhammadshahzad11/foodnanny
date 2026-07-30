<?php

namespace Database\Seeders;

use App\Models\FrontendSetting;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Dipokhalder\Settings\Facades\Settings;

class FrontendTableSeeder extends Seeder
{
    private array $translations = [
        'de' => [
            'frontend_hero_section_title'           => "Bio & leckeres Essen für Ihren Tisch",
            'frontend_hero_section_sub_title'       => "Wir haben eine Sammlung aller Arten von köstlichem Essen. Wählen Sie Ihr Lieblingsessen und bestellen Sie jetzt.",
            'frontend_app_section_title'            => "App herunterladen",
            'frontend_about_title'                  => "Warum sind wir die Besten?",
            'frontend_benefit_title'                => "Warum uns wählen?",
            'frontend_restaurant_section_title'     => "Registrieren Sie Ihr Restaurant",
            'frontend_restaurant_section_sub_title' => "Treten Sie FoodNanny bei und erreichen Sie mehr Kunden als je zuvor. Wir kümmern uns um die Lieferung, damit Sie sich auf das Essen konzentrieren können.",
            'frontend_delivery_section_title'       => "Werden Sie Lieferfahrer",
            'frontend_delivery_section_sub_title'   => "Die Freiheit, die Arbeit um Ihr Leben zu gestalten. Plus großartige Gebühren, Vergünstigungen und Rabatte.",
        ],
        'fr' => [
            'frontend_hero_section_title'           => "Nourriture bio et délicieuse pour votre table",
            'frontend_hero_section_sub_title'       => "Nous avons une collection de toutes sortes de délicieux plats. Choisissez ce que vous aimez et commandez maintenant.",
            'frontend_app_section_title'            => "Télécharger l'application",
            'frontend_about_title'                  => "Pourquoi sommes-nous les meilleurs ?",
            'frontend_benefit_title'                => "Pourquoi nous choisir ?",
            'frontend_restaurant_section_title'     => "Référencez votre restaurant",
            'frontend_restaurant_section_sub_title' => "Rejoignez FoodNanny et atteignez plus de clients que jamais. Nous gérons la livraison, vous pouvez vous concentrer sur la cuisine.",
            'frontend_delivery_section_title'       => "Devenez livreur",
            'frontend_delivery_section_sub_title'   => "La liberté d'adapter le travail à votre vie. Plus de grandes commissions, avantages et réductions.",
        ],
        'es' => [
            'frontend_hero_section_title'           => "Comida orgánica y deliciosa para su mesa",
            'frontend_hero_section_sub_title'       => "Tenemos una colección de todo tipo de comida deliciosa. Elija la comida que más le guste y pídala ahora.",
            'frontend_app_section_title'            => "Descargar la aplicación",
            'frontend_about_title'                  => "¿Por qué somos los mejores?",
            'frontend_benefit_title'                => "¿Por qué elegirnos?",
            'frontend_restaurant_section_title'     => "Registre su restaurante",
            'frontend_restaurant_section_sub_title' => "Únase a FoodNanny y llegue a más clientes que nunca. Nos encargamos de la entrega para que usted pueda concentrarse en la comida.",
            'frontend_delivery_section_title'       => "Conviértase en repartidor",
            'frontend_delivery_section_sub_title'   => "La libertad de adaptar el trabajo a su vida. Más grandes tarifas, ventajas y descuentos.",
        ],
        'ar' => [
            'frontend_hero_section_title'           => "طعام عضوي ولذيذ لمائدتك",
            'frontend_hero_section_sub_title'       => "لدينا مجموعة من جميع أنواع الأطعمة اللذيذة. اختر ما تحب وأطلب الآن.",
            'frontend_app_section_title'            => "حمّل التطبيق",
            'frontend_about_title'                  => "لماذا نحن الأفضل؟",
            'frontend_benefit_title'                => "لماذا تختارنا؟",
            'frontend_restaurant_section_title'     => "أضف مطعمك",
            'frontend_restaurant_section_sub_title' => "انضم إلى FoodNanny وابلغ عملاء أكثر من أي وقت مضى. نتولى التوصيل حتى تتفرغ للطعام.",
            'frontend_delivery_section_title'       => "كن مندوب توصيل",
            'frontend_delivery_section_sub_title'   => "حرية تنظيم وقت عملك. بالإضافة إلى رسوم ومميزات وخصومات رائعة.",
        ],
        'bn' => [
            'frontend_hero_section_title'           => "আপনার টেবিলের জন্য জৈব ও সুস্বাদু খাবার",
            'frontend_hero_section_sub_title'       => "আমাদের কাছে সব ধরনের সুস্বাদু খাবারের সংগ্রহ রয়েছে। আপনার পছন্দের খাবার বেছে নিন এবং এখনই অর্ডার করুন।",
            'frontend_app_section_title'            => "অ্যাপ ডাউনলোড করুন",
            'frontend_about_title'                  => "কেন আমরা সেরা?",
            'frontend_benefit_title'                => "কেন আমাদের বেছে নেবেন?",
            'frontend_restaurant_section_title'     => "আপনার রেস্তোরাঁ তালিকাভুক্ত করুন",
            'frontend_restaurant_section_sub_title' => "FoodNanny-তে যোগ দিন এবং আগের চেয়ে বেশি গ্রাহকের কাছে পৌঁছান। আমরা ডেলিভারি সামলাই, আপনি খাবারে মনোযোগ দিন।",
            'frontend_delivery_section_title'       => "ডেলিভারি বয় হন",
            'frontend_delivery_section_sub_title'   => "আপনার জীবনের সাথে মানানসই কাজের স্বাধীনতা। পাশাপাশি দুর্দান্ত ফি, সুবিধা এবং ছাড়।",
        ],
        'pt' => [
            'frontend_hero_section_title'           => "Comida orgânica e saborosa para a sua mesa",
            'frontend_hero_section_sub_title'       => "Temos uma coleção de todos os tipos de comida deliciosa. Escolha o que preferir e peça agora.",
            'frontend_app_section_title'            => "Baixe o aplicativo",
            'frontend_about_title'                  => "Por que somos os melhores?",
            'frontend_benefit_title'                => "Por que nos escolher?",
            'frontend_restaurant_section_title'     => "Cadastre o seu restaurante",
            'frontend_restaurant_section_sub_title' => "Junte-se ao FoodNanny e alcance mais clientes do que nunca. Cuidamos da entrega para você se concentrar na comida.",
            'frontend_delivery_section_title'       => "Torne-se um entregador",
            'frontend_delivery_section_sub_title'   => "A liberdade de conciliar o trabalho com a sua vida. Além de ótimas taxas, benefícios e descontos.",
        ],
    ];

    public function run()
    {
        Settings::group('frontend')->set([
            'frontend_hero_section_title'           => "Organic & Tasty Food for your Table",
            'frontend_hero_section_sub_title'       => "We have a collection of all kinds of delicious food here. Choose any food you like and buy it now.",
            'frontend_app_section_title'            => "Download the app",
            'frontend_app_section_android_app_link' => "https://play.google.com/store",
            'frontend_app_section_iso_app_link'     => "https://www.apple.com/app-store/",
            'frontend_about_title'                  => "Why We Are The Best?",
            'frontend_benefit_title'                => "Why Choose Us?",
            'frontend_restaurant_section_title'     => "List Your Restaurant",
            'frontend_restaurant_section_sub_title' => "Join FoodNanny and reach more customers then ever. We handle delivery, so you can focus on the food.",
            'frontend_delivery_section_title'       => "Become a Delivery Boy",
            'frontend_delivery_section_sub_title'   => "The freedom to fit work around your life. Plus great fees, perks and discounts.",
            'frontend_hero_section_image'           => "",
            'frontend_app_section_image'            => "",
            'frontend_restaurant_section_image'     => "",
            'frontend_delivery_section_image'       => ""
        ]);

        $settingKeys = array_keys($this->translations['de']);
        $settings = FrontendSetting::whereIn('key', $settingKeys)->get()->keyBy('key');

        foreach ($this->translations as $locale => $keys) {
            foreach ($keys as $settingKey => $value) {
                $setting = $settings->get($settingKey);
                if (!$setting) continue;

                Translation::updateOrCreate(
                    [
                        'translatable_type' => FrontendSetting::class,
                        'translatable_id'   => $setting->id,
                        'locale'            => $locale,
                        'key'               => 'value',
                    ],
                    ['value' => $value]
                );
            }
        }
    }
}
