<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SoftiesSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Product::truncate();
        Review::truncate();
        Schema::enableForeignKeyConstraints();

        // name, subtitle, desc, price, badge, premium, hero[emoji, hero_name, hero_price, hero_desc, order]|null
        $cookies = [
            ['Scarlett Velvet', 'Red velvet w/ keju', 'Red velvet base with white choco drizzle and a cream cheese bite. Soft, tangy, indulgent.', '5k', '🔥 Bestseller', false, ['🍪', 'Scarlett Velvet', 'Rp 5k', 'Red velvet + white choco drizzle. Our most loved.', 1]],
            ['Blur Whisper Crunch', 'Blue velvet w/ oreo crumble', 'Blue velvet dough with chunky Oreo crumble. Visually striking, crunchy-soft contrast.', '5k', null, false, ['💙', 'Blur Whisper Crunch', 'Rp 5k', null, 2]],
            ['Cinnamon Belle', 'Cinnamon w/ lotus crumble', 'Warm cinnamon cookie with lotus biscoff crumble. Autumnal spice, chewy center.', '5k', null, false, null],
            ['Bite Me Choco', 'Coklat w/ ceres', 'Chocolate dough rolled in chocolate ceres. Deep cocoa, chewy — crowd pleaser.', '5k', null, false, null],
            ["Cip's Brown Classic", 'Brown sugar w/ chocochips', 'Brown sugar base loaded with chocolate chips. Caramel-kissed, classic style.', '5k', null, false, null],
            ['Oh My Matcha', 'Matcha', 'Premium matcha dough with white choco drizzle. Earthy, subtly sweet, beautifully green.', '5k', '💚 Aesthetic', false, ['🍵', 'Oh My Matcha', 'Rp 5k', null, 3]],
            ['Pink Flash', 'Red velvet w/ strawberry inside + oreo', 'Red velvet + strawberry jam center, topped with full Oreo. The ultimate showstopper.', '6k', '⭐ Premium', true, null],
            ['Cookie n Cream', 'Cookie n cream', 'Oreo-studded base with cream filling inside. Cookies & cream in cookie form.', '6k', null, true, null],
            ['Berry Pop Rocks', 'Brown sugar w/ blueberry in side', 'Brown sugar dough with a fresh blueberry pocket. Fruity burst inside chewy exterior.', '5k', null, false, null],
            ['Marsh Sprinkle Bliss', 'Brown sugar w/ rainbow ceres', 'Brown sugar base topped with toasted marshmallow and rainbow sprinkles. Party-ready.', '6k', '🎊 Party', true, null],
        ];

        $brownies = [
            ['Basic Choco Brownies', '7 bites in 1 pack', 'Pure dark chocolate brownie. Fudgy, intense — the foundation of the entire lineup.', '12k', '💛 OG', false, ['🍫', 'Basic Choco Brownie', 'Rp 12k / pack', null, 4]],
            ['Cookie Brownies (Brockie)', '7 bites in 1 pack', 'Brownie base infused with cookie crumble. A delicious hybrid of two classics.', '13k', null, false, null],
            ['Marshmallow Brownies', '7 bites in 1 pack', 'Gooey marshmallow swirled into dark brownie. Rocky road vibes, incredibly soft.', '13k', null, false, null],
            ['Chococips Brownies', '7 bites in 1 pack', 'Fudgy brownie studded with premium chocolate chips throughout every bite.', '13k', null, false, null],
            ['Cheese Brownies', '7 bites in 1 pack', 'Cream cheese ribboned through rich chocolate brownie. Sweet-salty balance perfected.', '13k', null, false, null],
            ['Lotus Biscoff Brownies', '7 bites in 1 pack', 'Premium lotus caramel biscoff topping on dense brownie. The cult-favorite flavor.', '14k', '🏆 Fan Fav', true, null],
            ['Oreo Brownies', '7 bites in 1 pack', 'Oreo pieces embedded in deep chocolate brownie. Crunchy pockets throughout.', '14k', null, true, null],
            ['Melted Series', '7 bites + 20ml glaze sachet', 'Brownie bites + a glaze sachet to pour yourself. Choose: choco, matcha, or caramel.', '14k', '🔥 Interactive', true, ['🌟', 'Melted Series', 'Rp 14k', null, 5]],
            ['Birthday Brownie (Loyang)', 'Full tray • Order 1 week in advance', 'Custom full-tray birthday brownie with personalized toppings. DM for pricing and details.', 'DM', null, false, null],
        ];

        $i = 0;
        foreach ([['cookie', $cookies], ['brownie', $brownies]] as [$cat, $list]) {
            foreach ($list as $p) {
                Product::create([
                    'category' => $cat, 'name' => $p[0], 'subtitle' => $p[1], 'description' => $p[2],
                    'price_label' => $p[3], 'price' => $p[3] === 'DM' ? null : ((int) $p[3]) * 1000,
                    'lead_days' => str_contains($p[0], 'Birthday') ? 5 : 2,
                    'badge' => $p[4], 'is_premium' => $p[5],
                    'is_featured' => $p[6] !== null,
                    'emoji' => $p[6][0] ?? null, 'hero_name' => $p[6][1] ?? null,
                    'featured_price' => $p[6][2] ?? null, 'hero_desc' => $p[6][3] ?? null,
                    'featured_order' => $p[6][4] ?? 0,
                    'sort_order' => ++$i,
                ]);
            }
        }

        $reviews = [
            ['omg bestieeee, cookies nya enak bangettttt, sukakkk, uenakkk pol plis kapan-kapan mau beli lagi, gak nyesel beli banyakkk', 'Customer via WhatsApp', true],
            ['haloo kak, tadi udah coba beberapa, overall cookiesnya enakk, lembut, chewy dan enggak kemanisan. enakkk banget 🤍🤍', 'Customer via WhatsApp', false],
            ['enaakk, mau lagi, ENAKK BGTT BGTT BGTT BGTTTT, lagi dongg, beso jadii bikin cookies lagii gaaa, yeah i love it', 'Customer via WhatsApp', false],
            ['klo bwt packaging sin ga perlu ditanya lagi ynh, udh bagus bet anjay. trs ketahanannya jg lumayan — waktu ke Kelud itu aku bawa cookiesmu dan masih enak!', 'Customer via WhatsApp • Traveled to Kelud 🌋', true],
            ['untuk kritik sihh menurut gw pribadi yhh, itu gaada, udah pas bgt itu, apalagi lu jual dengan harga yg murah juga kann, jadi menurut gw klo lu mau jualan lagi, jualan yg ky dulu udah pas mnurut gw', 'Customer via WhatsApp', false],
            ['untuk tekstur kan preferensi masing-masing yh, nah cookies lu klo menurut gwe tingkat soft nya pas sihh, soalnya gw sendiri kurang suka tekstur cookies yg keras. pas banget!', 'Customer via WhatsApp', false],
        ];
        foreach ($reviews as $n => $r) {
            Review::create(['body' => $r[0], 'author' => $r[1], 'stars' => 5, 'highlight' => $r[2], 'status' => 'approved', 'sort_order' => $n + 1]);
        }
    }
}
