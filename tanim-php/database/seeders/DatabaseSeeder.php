<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── ADMIN ──────────────────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Admin Tanim',
            'email'             => 'admin@tanim.ph',
            'role'              => 'admin',
            'password'          => Hash::make('admin123'),
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // ── BUYERS ─────────────────────────────────────────────────────
        $buyers = collect();
        foreach ([
            ['Maria Santos',      'buyer1@tanim.ph'],
            ['Pedro Reyes',       'buyer2@tanim.ph'],
            ['Ana Buencamino',    'buyer3@tanim.ph'],
            ['Maria Santos Demo', 'buyer@tanim.ph'],
        ] as [$name, $email]) {
            $buyers->push(User::create([
                'name'              => $name,
                'email'             => $email,
                'role'              => 'buyer',
                'password'          => Hash::make('password'),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]));
        }

        // ── PRODUCTS (owned by Tanim/admin) ────────────────────────────
        $products = [
            ['name'=>'Fresh Kangkong','category'=>'Vegetables','price'=>25,'unit'=>'bundle','stock'=>100,'description'=>'Freshly harvested water spinach from Laguna. Rich in iron and vitamins.','farm_location'=>'Laguna, Luzon','harvest_date'=>'2026-03-12'],
            ['name'=>'Ampalaya (Bitter Gourd)','category'=>'Vegetables','price'=>60,'unit'=>'kg','stock'=>80,'description'=>'Organically grown bitter gourd from highland farms.','farm_location'=>'Benguet, Cordillera','harvest_date'=>'2026-03-13'],
            ['name'=>'Native Kamote','category'=>'Vegetables','price'=>45,'unit'=>'kg','stock'=>150,'description'=>'Naturally sweet potatoes from highland farms.','farm_location'=>'Nueva Ecija, Central Luzon','harvest_date'=>'2026-03-10'],
            ['name'=>'Eggplant (Talong)','category'=>'Vegetables','price'=>55,'unit'=>'kg','stock'=>90,'description'=>'Glossy firm eggplants ideal for sinigang and tortang talong.','farm_location'=>'Bukidnon, Mindanao','harvest_date'=>'2026-03-11'],
            ['name'=>'Pechay Baguio','category'=>'Vegetables','price'=>30,'unit'=>'kg','stock'=>120,'description'=>'Crisp highland pechay from Benguet. Great for stir-fry and soups.','farm_location'=>'Benguet, Cordillera','harvest_date'=>'2026-03-14'],
            ['name'=>'Sitaw (String Beans)','category'=>'Vegetables','price'=>40,'unit'=>'bundle','stock'=>70,'description'=>'Fresh long beans, tender and crunchy.','farm_location'=>'Laguna, Luzon','harvest_date'=>'2026-03-13'],
            ['name'=>'Lakatan Banana','category'=>'Fruits','price'=>80,'unit'=>'kg','stock'=>200,'description'=>'Sweet and creamy Lakatan bananas from Davao. 100% natural.','farm_location'=>'Davao del Sur, Mindanao','harvest_date'=>'2026-03-09'],
            ['name'=>'Philippine Mango (Carabao)','category'=>'Fruits','price'=>120,'unit'=>'kg','stock'=>60,'description'=>'World-famous Carabao mango from Guimaras.','farm_location'=>'Guimaras, Western Visayas','harvest_date'=>'2026-03-08'],
            ['name'=>'Dalandan (Philippine Orange)','category'=>'Fruits','price'=>75,'unit'=>'kg','stock'=>90,'description'=>'Freshly picked Dalandan high in vitamin C.','farm_location'=>'Batangas, Luzon','harvest_date'=>'2026-03-12'],
            ['name'=>'Papaya (Solo)','category'=>'Fruits','price'=>50,'unit'=>'piece','stock'=>40,'description'=>'Ripe papaya with bright orange flesh, enzyme-rich.','farm_location'=>'Cavite, Luzon','harvest_date'=>'2026-03-11'],
            ['name'=>'Dinorado Rice','category'=>'Grains & Rice','price'=>65,'unit'=>'kg','stock'=>500,'description'=>'Premium Dinorado white rice. Soft and aromatic.','farm_location'=>'Nueva Ecija, Central Luzon','harvest_date'=>'2026-02-28'],
            ['name'=>'Black Rice (Pirurutong)','category'=>'Grains & Rice','price'=>95,'unit'=>'kg','stock'=>80,'description'=>'Heritage black glutinous rice from Cordillera. Rich in antioxidants.','farm_location'=>'Kalinga, Cordillera','harvest_date'=>'2026-02-20'],
            ['name'=>'White Corn Kernels','category'=>'Grains & Rice','price'=>40,'unit'=>'kg','stock'=>300,'description'=>'Dried white corn from Bukidnon. Great for grits and soups.','farm_location'=>'Bukidnon, Mindanao','harvest_date'=>'2026-02-25'],
            ['name'=>'Gabi (Taro Root)','category'=>'Root Crops','price'=>50,'unit'=>'kg','stock'=>100,'description'=>'Large creamy taro root. Essential for sinigang and ginataan.','farm_location'=>'Quezon, Luzon','harvest_date'=>'2026-03-06'],
            ['name'=>'Cassava (Kamoteng Kahoy)','category'=>'Root Crops','price'=>35,'unit'=>'kg','stock'=>200,'description'=>'Fresh cassava from Mindanao. Used for puto and cakes.','farm_location'=>'South Cotabato, Mindanao','harvest_date'=>'2026-03-05'],
            ['name'=>'Lemongrass (Tanglad)','category'=>'Herbs & Spices','price'=>20,'unit'=>'bundle','stock'=>80,'description'=>'Aromatic lemongrass fresh from the province. Great for teas.','farm_location'=>'Laguna, Luzon','harvest_date'=>'2026-03-13'],
            ['name'=>'Pandan Leaves','category'=>'Herbs & Spices','price'=>15,'unit'=>'bundle','stock'=>100,'description'=>'Fresh pandan leaves for flavoring rice and kakanin.','farm_location'=>'Laguna, Luzon','harvest_date'=>'2026-03-14'],
            ['name'=>'Turmeric Root (Luyang Dilaw)','category'=>'Herbs & Spices','price'=>55,'unit'=>'kg','stock'=>60,'description'=>'Fresh golden turmeric root. Anti-inflammatory properties.','farm_location'=>'Cebu, Visayas','harvest_date'=>'2026-03-07'],
        ];

        $texts = [
            5 => ['Super fresh talaga! Highly recommend!', 'Best quality from Tanim. Will order again!', 'Napakalusog ng produkto!'],
            4 => ['Very good quality. Delivery was fast.', 'Fresh and tasty. Sulit na sulit sa presyo.', 'Mabuting kalidad. Would buy again.'],
            3 => ['Okay lang. Konting pagbabago sa packaging.', 'Average quality but fresh naman.'],
            2 => ['Medyo nalanta na pagdating. Sana mapabuti.'],
        ];

        foreach ($products as $data) {
            $product = Product::create(array_merge($data, ['user_id' => $admin->id]));
            foreach ($buyers->shuffle()->take(rand(2, 4)) as $buyer) {
                $r = rand(3, 5);
                Review::create(['user_id'=>$buyer->id,'product_id'=>$product->id,'rating'=>$r,'comment'=>$texts[$r][array_rand($texts[$r])]]);
            }
        }

        // ── EXPENSES ───────────────────────────────────────────────────
        foreach ([
            ['electricity',   'Meralco Bill — March 2026',          4820,  '2026-03-05', true,  'monthly', null],
            ['water',         'Manila Water — March 2026',           980,   '2026-03-05', true,  'monthly', null],
            ['internet',      'PLDT Fiber — March 2026',             2499,  '2026-03-01', true,  'monthly', null],
            ['maintenance',   'Server Maintenance & Hosting',        3500,  '2026-03-01', true,  'monthly', 'VPS hosting and domain renewal'],
            ['salary',        'March 2026 Payroll',                  65000, '2026-03-15', true,  'monthly', 'All employees combined'],
            ['delivery',      'Delivery Partner Fees — Week 1',      1200,  '2026-03-07', false, null,      'Lalamove batch'],
            ['delivery',      'Delivery Partner Fees — Week 2',      1350,  '2026-03-14', false, null,      null],
            ['restock',       'Packaging Materials & Supplies',      2800,  '2026-03-10', false, null,      'Eco-friendly bags and labels'],
            ['upcoming_stock','Advance Payment — Farmer Batch Q2',   15000, '2026-03-12', false, null,      'Advance for Q2 harvest'],
            ['maintenance',   'Office Supplies Restock',             650,   '2026-03-08', false, null,      null],
            ['other',         'Marketing — Social Media Boost',      1500,  '2026-03-11', false, null,      'FB/Instagram ads March'],
            ['electricity',   'Meralco Bill — February 2026',        4650,  '2026-02-05', true,  'monthly', null],
        ] as [$type,$label,$amount,$date,$recurring,$period,$notes]) {
            Expense::create([
                'type'             => $type,
                'label'            => $label,
                'amount'           => $amount,
                'expense_date'     => $date,
                'recurring'        => $recurring,
                'recurring_period' => $period,
                'notes'            => $notes,
            ]);
        }

        // ── EMPLOYEES ──────────────────────────────────────────────────
        foreach ([
            ['Carlo Mendoza',      'Operations Manager',   'Management', 28000, 5000, '2024-01-10'],
            ['Liza Fernandez',     'Customer Support',     'Operations', 18000, 2000, '2024-03-15'],
            ['Romer Santos',       'Delivery Coordinator', 'Logistics',  17500, 1500, '2024-06-01'],
            ['Jessa Buenaventura', 'Web Developer',        'Technology', 32000, 4000, '2023-11-20'],
            ['Mark Villanueva',    'Social Media Manager', 'Marketing',  20000, 2500, '2024-08-05'],
            ['Rosa Alcantara',     'Accounting Staff',     'Finance',    22000, 2000, '2024-02-14'],
        ] as [$name,$pos,$dept,$sal,$bonus,$hire]) {
            Employee::create(['name'=>$name,'position'=>$pos,'department'=>$dept,'base_salary'=>$sal,'bonus'=>$bonus,'hire_date'=>$hire,'status'=>'active']);
        }
    }
}
