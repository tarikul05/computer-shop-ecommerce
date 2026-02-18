<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Brand;
use App\Modules\Product\Models\SpecificationGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create Specification Groups first
        $specGroups = [
            'Basic Information' => 1,
            'Processor' => 2,
            'Memory' => 3,
            'Storage' => 4,
            'Display' => 5,
            'Graphics' => 6,
            'Connectivity' => 7,
            'Power' => 8,
            'Physical' => 9,
            'Warranty' => 10,
        ];

        foreach ($specGroups as $name => $order) {
            SpecificationGroup::create([
                'name' => $name,
                'sort_order' => $order,
            ]);
        }

        // Get categories and brands
        $categories = Category::all()->keyBy('name');
        $brands = Brand::all()->keyBy('name');
        $groups = SpecificationGroup::all()->keyBy('name');

        $products = $this->getProductsData();

        foreach ($products as $productData) {
            $category = $categories[$productData['category']] ?? null;
            $brand = $brands[$productData['brand']] ?? null;

            if (!$category || !$brand) {
                continue;
            }

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']) . '-' . Str::random(4),
                'sku' => $productData['sku'],
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'price' => $productData['price'],
                'compare_price' => $productData['compare_price'] ?? null,
                'cost_price' => $productData['cost_price'] ?? null,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'quantity' => $productData['stock'] ?? rand(5, 100),
                'low_stock_threshold' => 5,
                'is_active' => true,
                'is_featured' => $productData['featured'] ?? false,
                'meta_title' => $productData['name'],
                'meta_description' => $productData['short_description'],
            ]);

            // Add specifications
            if (!empty($productData['specs'])) {
                foreach ($productData['specs'] as $groupName => $specs) {
                    $group = $groups[$groupName] ?? null;
                    if ($group) {
                        $order = 0;
                        foreach ($specs as $name => $value) {
                            $product->specifications()->create([
                                'specification_group_id' => $group->id,
                                'name' => $name,
                                'value' => $value,
                                'sort_order' => $order++,
                            ]);
                        }
                    }
                }
            }

            // Add placeholder images
            for ($i = 1; $i <= rand(1, 3); $i++) {
                $product->images()->create([
                    'image' => 'products/placeholder-' . $product->id . '-' . $i . '.jpg',
                    'alt_text' => $product->name,
                    'sort_order' => $i - 1,
                    'is_primary' => $i === 1,
                ]);
            }
        }

        $this->command->info('Products seeded: ' . Product::count() . ' total');
    }

    private function getProductsData(): array
    {
        return [
            // Processors
            [
                'name' => 'Intel Core i9-14900K Processor',
                'sku' => 'INT-I9-14900K',
                'category' => 'Processor',
                'brand' => 'Intel',
                'price' => 62500,
                'compare_price' => 68000,
                'cost_price' => 55000,
                'short_description' => '24 Cores, 32 Threads, Up to 6.0GHz, 36MB Cache',
                'description' => 'The Intel Core i9-14900K is the flagship processor featuring 24 cores (8 P-cores + 16 E-cores) and 32 threads. With boost speeds up to 6.0GHz and 36MB of Intel Smart Cache, it delivers exceptional performance for gaming, content creation, and multitasking.',
                'featured' => true,
                'stock' => 25,
                'specs' => [
                    'Processor' => [
                        'Cores' => '24 (8P + 16E)',
                        'Threads' => '32',
                        'Base Clock (P-Core)' => '3.2 GHz',
                        'Max Turbo Clock' => '6.0 GHz',
                        'Cache' => '36 MB Intel Smart Cache',
                        'TDP' => '125W',
                        'Socket' => 'LGA 1700',
                    ],
                    'Warranty' => [
                        'Warranty' => '3 Years',
                    ],
                ],
            ],
            [
                'name' => 'Intel Core i7-14700K Processor',
                'sku' => 'INT-I7-14700K',
                'category' => 'Processor',
                'brand' => 'Intel',
                'price' => 45500,
                'compare_price' => 48000,
                'short_description' => '20 Cores, 28 Threads, Up to 5.6GHz, 33MB Cache',
                'description' => 'The Intel Core i7-14700K features 20 cores and 28 threads for excellent gaming and productivity performance.',
                'featured' => true,
                'stock' => 30,
                'specs' => [
                    'Processor' => [
                        'Cores' => '20 (8P + 12E)',
                        'Threads' => '28',
                        'Max Turbo Clock' => '5.6 GHz',
                        'Cache' => '33 MB Intel Smart Cache',
                        'Socket' => 'LGA 1700',
                    ],
                ],
            ],
            [
                'name' => 'Intel Core i5-14600K Processor',
                'sku' => 'INT-I5-14600K',
                'category' => 'Processor',
                'brand' => 'Intel',
                'price' => 32500,
                'short_description' => '14 Cores, 20 Threads, Up to 5.3GHz, 24MB Cache',
                'description' => 'Great value gaming processor with 14 cores and excellent single-threaded performance.',
                'stock' => 45,
                'specs' => [
                    'Processor' => [
                        'Cores' => '14 (6P + 8E)',
                        'Threads' => '20',
                        'Max Turbo Clock' => '5.3 GHz',
                        'Cache' => '24 MB',
                        'Socket' => 'LGA 1700',
                    ],
                ],
            ],
            [
                'name' => 'AMD Ryzen 9 7950X Processor',
                'sku' => 'AMD-R9-7950X',
                'category' => 'Processor',
                'brand' => 'AMD',
                'price' => 58500,
                'compare_price' => 65000,
                'short_description' => '16 Cores, 32 Threads, Up to 5.7GHz, 80MB Cache',
                'description' => 'The AMD Ryzen 9 7950X is a powerhouse with 16 cores and 32 threads, perfect for content creators and heavy multitaskers.',
                'featured' => true,
                'stock' => 20,
                'specs' => [
                    'Processor' => [
                        'Cores' => '16',
                        'Threads' => '32',
                        'Base Clock' => '4.5 GHz',
                        'Max Boost Clock' => '5.7 GHz',
                        'Total Cache' => '80 MB',
                        'TDP' => '170W',
                        'Socket' => 'AM5',
                    ],
                ],
            ],
            [
                'name' => 'AMD Ryzen 7 7800X3D Processor',
                'sku' => 'AMD-R7-7800X3D',
                'category' => 'Processor',
                'brand' => 'AMD',
                'price' => 48500,
                'short_description' => '8 Cores, 16 Threads, Up to 5.0GHz, 104MB Cache with 3D V-Cache',
                'description' => 'The ultimate gaming processor with AMD 3D V-Cache technology for exceptional gaming performance.',
                'featured' => true,
                'stock' => 15,
                'specs' => [
                    'Processor' => [
                        'Cores' => '8',
                        'Threads' => '16',
                        'Max Boost Clock' => '5.0 GHz',
                        'Total Cache' => '104 MB (96MB 3D V-Cache)',
                        'TDP' => '120W',
                        'Socket' => 'AM5',
                    ],
                ],
            ],
            [
                'name' => 'AMD Ryzen 5 7600X Processor',
                'sku' => 'AMD-R5-7600X',
                'category' => 'Processor',
                'brand' => 'AMD',
                'price' => 27500,
                'short_description' => '6 Cores, 12 Threads, Up to 5.3GHz, 38MB Cache',
                'description' => 'Excellent mid-range gaming processor with great value.',
                'stock' => 40,
                'specs' => [
                    'Processor' => [
                        'Cores' => '6',
                        'Threads' => '12',
                        'Max Boost Clock' => '5.3 GHz',
                        'Cache' => '38 MB',
                        'Socket' => 'AM5',
                    ],
                ],
            ],

            // Graphics Cards
            [
                'name' => 'NVIDIA GeForce RTX 4090 Founders Edition',
                'sku' => 'NV-RTX4090-FE',
                'category' => 'Graphics Card',
                'brand' => 'NVIDIA',
                'price' => 225000,
                'compare_price' => 245000,
                'short_description' => '24GB GDDR6X, Ada Lovelace Architecture, DLSS 3',
                'description' => 'The ultimate graphics card for gaming and content creation. Features 24GB GDDR6X memory and the latest Ada Lovelace architecture.',
                'featured' => true,
                'stock' => 5,
                'specs' => [
                    'Graphics' => [
                        'GPU' => 'AD102',
                        'CUDA Cores' => '16384',
                        'Memory' => '24GB GDDR6X',
                        'Memory Bus' => '384-bit',
                        'Boost Clock' => '2520 MHz',
                        'TDP' => '450W',
                    ],
                    'Connectivity' => [
                        'Display Outputs' => '1x HDMI 2.1, 3x DisplayPort 1.4a',
                    ],
                ],
            ],
            [
                'name' => 'ASUS ROG Strix GeForce RTX 4080 SUPER OC',
                'sku' => 'ASUS-RTX4080S-STRIX',
                'category' => 'Graphics Card',
                'brand' => 'ASUS',
                'price' => 145000,
                'short_description' => '16GB GDDR6X, ROG Strix Cooling, RGB',
                'description' => 'Premium RTX 4080 SUPER with ASUS ROG Strix cooling solution and Aura Sync RGB.',
                'featured' => true,
                'stock' => 10,
                'specs' => [
                    'Graphics' => [
                        'CUDA Cores' => '10240',
                        'Memory' => '16GB GDDR6X',
                        'Boost Clock' => '2610 MHz (OC Mode)',
                        'TDP' => '320W',
                    ],
                ],
            ],
            [
                'name' => 'Gigabyte GeForce RTX 4070 Ti SUPER Gaming OC',
                'sku' => 'GB-RTX4070TIS-GOC',
                'category' => 'Graphics Card',
                'brand' => 'Gigabyte',
                'price' => 98500,
                'short_description' => '16GB GDDR6X, WINDFORCE Cooling, RGB Fusion 2.0',
                'description' => 'Excellent 1440p gaming graphics card with Gigabyte WINDFORCE cooling.',
                'stock' => 15,
                'specs' => [
                    'Graphics' => [
                        'CUDA Cores' => '8448',
                        'Memory' => '16GB GDDR6X',
                        'Boost Clock' => '2640 MHz',
                    ],
                ],
            ],
            [
                'name' => 'MSI GeForce RTX 4070 SUPER Ventus 3X OC',
                'sku' => 'MSI-RTX4070S-V3X',
                'category' => 'Graphics Card',
                'brand' => 'MSI',
                'price' => 72500,
                'short_description' => '12GB GDDR6X, TORX Fan 5.0, Afterburner OC',
                'description' => 'Great 1440p gaming performance with MSI Ventus cooling.',
                'stock' => 20,
                'specs' => [
                    'Graphics' => [
                        'CUDA Cores' => '7168',
                        'Memory' => '12GB GDDR6X',
                        'Boost Clock' => '2520 MHz',
                    ],
                ],
            ],
            [
                'name' => 'ASUS Dual GeForce RTX 4060 OC Edition',
                'sku' => 'ASUS-RTX4060-DUAL',
                'category' => 'Graphics Card',
                'brand' => 'ASUS',
                'price' => 38500,
                'short_description' => '8GB GDDR6, Axial-tech Fan Design',
                'description' => 'Affordable 1080p gaming graphics card with efficient cooling.',
                'stock' => 30,
                'specs' => [
                    'Graphics' => [
                        'CUDA Cores' => '3072',
                        'Memory' => '8GB GDDR6',
                        'Boost Clock' => '2535 MHz',
                        'TDP' => '115W',
                    ],
                ],
            ],

            // Motherboards
            [
                'name' => 'ASUS ROG Maximus Z790 Hero',
                'sku' => 'ASUS-Z790-HERO',
                'category' => 'Motherboard',
                'brand' => 'ASUS',
                'price' => 68500,
                'short_description' => 'Intel Z790 Chipset, DDR5, WiFi 6E, 2.5G LAN',
                'description' => 'Premium Intel Z790 motherboard for enthusiast builds with top-tier features.',
                'featured' => true,
                'stock' => 10,
                'specs' => [
                    'Basic Information' => [
                        'Chipset' => 'Intel Z790',
                        'Socket' => 'LGA 1700',
                        'Form Factor' => 'ATX',
                    ],
                    'Memory' => [
                        'Memory Slots' => '4x DDR5',
                        'Max Memory' => '192GB',
                        'Memory Speed' => 'Up to DDR5-7800 (OC)',
                    ],
                    'Connectivity' => [
                        'PCIe Slots' => '1x PCIe 5.0 x16, 1x PCIe 4.0 x16',
                        'M.2 Slots' => '5x M.2 (PCIe 4.0)',
                        'SATA Ports' => '4x SATA 6Gb/s',
                        'USB Ports' => 'USB 3.2 Gen 2x2, USB 3.2 Gen 2, USB 2.0',
                        'Network' => '2.5G LAN, WiFi 6E, Bluetooth 5.3',
                    ],
                ],
            ],
            [
                'name' => 'MSI MAG B760 Tomahawk WiFi',
                'sku' => 'MSI-B760-TOM-WIFI',
                'category' => 'Motherboard',
                'brand' => 'MSI',
                'price' => 24500,
                'short_description' => 'Intel B760 Chipset, DDR5, WiFi 6E, 2.5G LAN',
                'description' => 'Feature-rich mid-range motherboard with excellent VRM for Intel 14th Gen.',
                'stock' => 25,
                'specs' => [
                    'Basic Information' => [
                        'Chipset' => 'Intel B760',
                        'Socket' => 'LGA 1700',
                        'Form Factor' => 'ATX',
                    ],
                    'Memory' => [
                        'Memory Slots' => '4x DDR5',
                        'Max Memory' => '128GB',
                    ],
                ],
            ],
            [
                'name' => 'Gigabyte B650 AORUS Elite AX',
                'sku' => 'GB-B650-AORUS-AX',
                'category' => 'Motherboard',
                'brand' => 'Gigabyte',
                'price' => 26500,
                'short_description' => 'AMD B650 Chipset, DDR5, WiFi 6E, 2.5G LAN',
                'description' => 'Great AMD AM5 motherboard with premium features at mid-range price.',
                'stock' => 20,
                'specs' => [
                    'Basic Information' => [
                        'Chipset' => 'AMD B650',
                        'Socket' => 'AM5',
                        'Form Factor' => 'ATX',
                    ],
                ],
            ],
            [
                'name' => 'ASRock B650M Pro RS WiFi',
                'sku' => 'ASR-B650M-PRO-RS',
                'category' => 'Motherboard',
                'brand' => 'ASRock',
                'price' => 17500,
                'short_description' => 'AMD B650 Chipset, DDR5, Micro-ATX, WiFi 6E',
                'description' => 'Compact and affordable AMD AM5 motherboard with WiFi.',
                'stock' => 35,
            ],

            // RAM
            [
                'name' => 'G.Skill Trident Z5 RGB DDR5-6400 32GB (2x16GB)',
                'sku' => 'GSKILL-Z5-6400-32',
                'category' => 'RAM',
                'brand' => 'G.Skill',
                'price' => 18500,
                'short_description' => 'DDR5-6400 CL32, RGB, Intel XMP 3.0',
                'description' => 'High-performance DDR5 memory with stunning RGB lighting.',
                'featured' => true,
                'stock' => 30,
                'specs' => [
                    'Memory' => [
                        'Capacity' => '32GB (2x16GB)',
                        'Type' => 'DDR5',
                        'Speed' => '6400 MT/s',
                        'Latency' => 'CL32-39-39-102',
                        'Voltage' => '1.35V',
                    ],
                ],
            ],
            [
                'name' => 'Corsair Vengeance DDR5-5600 32GB (2x16GB)',
                'sku' => 'COR-VENG-5600-32',
                'category' => 'RAM',
                'brand' => 'Corsair',
                'price' => 13500,
                'short_description' => 'DDR5-5600 CL36, Black Heatspreader',
                'description' => 'Reliable DDR5 memory from Corsair with excellent compatibility.',
                'stock' => 40,
            ],
            [
                'name' => 'Kingston Fury Beast DDR5-5200 16GB (2x8GB)',
                'sku' => 'KNG-FURY-5200-16',
                'category' => 'RAM',
                'brand' => 'Kingston',
                'price' => 7500,
                'short_description' => 'DDR5-5200 CL40, Budget DDR5 Kit',
                'description' => 'Affordable DDR5 kit for budget builds.',
                'stock' => 50,
            ],
            [
                'name' => 'Corsair Vengeance LPX DDR4-3200 16GB (2x8GB)',
                'sku' => 'COR-LPX-3200-16',
                'category' => 'RAM',
                'brand' => 'Corsair',
                'price' => 4800,
                'short_description' => 'DDR4-3200 CL16, Low Profile',
                'description' => 'Popular DDR4 memory kit with great compatibility.',
                'stock' => 60,
            ],

            // Storage - SSD
            [
                'name' => 'Samsung 990 Pro 2TB NVMe SSD',
                'sku' => 'SAM-990PRO-2TB',
                'category' => 'NVMe SSD',
                'brand' => 'Samsung',
                'price' => 22500,
                'compare_price' => 25000,
                'short_description' => '2TB, PCIe 4.0 x4, 7450MB/s Read, 6900MB/s Write',
                'description' => 'Flagship NVMe SSD with exceptional performance for gaming and creative workloads.',
                'featured' => true,
                'stock' => 25,
                'specs' => [
                    'Storage' => [
                        'Capacity' => '2TB',
                        'Interface' => 'PCIe 4.0 x4 NVMe',
                        'Sequential Read' => '7450 MB/s',
                        'Sequential Write' => '6900 MB/s',
                        'Form Factor' => 'M.2 2280',
                        'TBW' => '1200 TBW',
                    ],
                ],
            ],
            [
                'name' => 'Samsung 990 Pro 1TB NVMe SSD',
                'sku' => 'SAM-990PRO-1TB',
                'category' => 'NVMe SSD',
                'brand' => 'Samsung',
                'price' => 13500,
                'short_description' => '1TB, PCIe 4.0 x4, 7450MB/s Read',
                'description' => 'High-performance NVMe SSD for enthusiasts.',
                'stock' => 35,
            ],
            [
                'name' => 'WD Black SN850X 1TB NVMe SSD',
                'sku' => 'WD-SN850X-1TB',
                'category' => 'NVMe SSD',
                'brand' => 'Western Digital',
                'price' => 11500,
                'short_description' => '1TB, PCIe 4.0 x4, 7300MB/s Read',
                'description' => 'Gaming-focused NVMe SSD with excellent sustained performance.',
                'stock' => 30,
            ],
            [
                'name' => 'Crucial P3 Plus 1TB NVMe SSD',
                'sku' => 'CRU-P3PLUS-1TB',
                'category' => 'NVMe SSD',
                'brand' => 'Crucial',
                'price' => 6500,
                'short_description' => '1TB, PCIe 4.0 x4, 5000MB/s Read',
                'description' => 'Budget-friendly PCIe 4.0 NVMe SSD.',
                'stock' => 45,
            ],
            [
                'name' => 'Samsung 870 EVO 1TB SATA SSD',
                'sku' => 'SAM-870EVO-1TB',
                'category' => 'SSD',
                'brand' => 'Samsung',
                'price' => 9500,
                'short_description' => '1TB, SATA III, 560MB/s Read',
                'description' => 'Reliable SATA SSD for system upgrades.',
                'stock' => 40,
            ],

            // Monitors
            [
                'name' => 'LG 27GP950-B 27" 4K Gaming Monitor',
                'sku' => 'LG-27GP950B',
                'category' => 'Gaming Monitor',
                'brand' => 'LG',
                'price' => 85000,
                'short_description' => '27" 4K Nano IPS, 160Hz, 1ms, HDMI 2.1, G-Sync Compatible',
                'description' => 'Premium 4K gaming monitor with HDMI 2.1 for next-gen consoles.',
                'featured' => true,
                'stock' => 8,
                'specs' => [
                    'Display' => [
                        'Screen Size' => '27 inches',
                        'Resolution' => '3840 x 2160 (4K UHD)',
                        'Panel Type' => 'Nano IPS',
                        'Refresh Rate' => '160Hz (OC)',
                        'Response Time' => '1ms GtG',
                        'HDR' => 'VESA DisplayHDR 600',
                    ],
                    'Connectivity' => [
                        'Inputs' => '2x HDMI 2.1, 1x DisplayPort 1.4, USB Hub',
                    ],
                ],
            ],
            [
                'name' => 'ASUS ROG Swift PG279QM 27" Gaming Monitor',
                'sku' => 'ASUS-PG279QM',
                'category' => 'Gaming Monitor',
                'brand' => 'ASUS',
                'price' => 72000,
                'short_description' => '27" 1440p IPS, 240Hz, 1ms, G-Sync',
                'description' => 'Ultimate 1440p gaming monitor with 240Hz refresh rate.',
                'featured' => true,
                'stock' => 10,
            ],
            [
                'name' => 'Samsung Odyssey G7 32" Curved Gaming Monitor',
                'sku' => 'SAM-G7-32',
                'category' => 'Curved Monitor',
                'brand' => 'Samsung',
                'price' => 58000,
                'short_description' => '32" 1440p VA, 240Hz, 1ms, 1000R Curve',
                'description' => 'Immersive curved gaming monitor with deep blacks.',
                'stock' => 12,
            ],
            [
                'name' => 'Dell UltraSharp U2723QE 27" 4K Monitor',
                'sku' => 'DELL-U2723QE',
                'category' => 'Professional Monitor',
                'brand' => 'Dell',
                'price' => 68000,
                'short_description' => '27" 4K IPS, USB-C Hub, 100% sRGB, 98% DCI-P3',
                'description' => 'Professional color-accurate monitor for creators.',
                'stock' => 15,
            ],
            [
                'name' => 'BenQ MOBIUZ EX2710Q 27" Gaming Monitor',
                'sku' => 'BENQ-EX2710Q',
                'category' => 'Gaming Monitor',
                'brand' => 'BenQ',
                'price' => 42000,
                'short_description' => '27" 1440p IPS, 165Hz, 1ms, HDRi',
                'description' => 'Great all-around gaming monitor with excellent speakers.',
                'stock' => 18,
            ],
            [
                'name' => 'AOC 24G2SP 24" Gaming Monitor',
                'sku' => 'AOC-24G2SP',
                'category' => 'Gaming Monitor',
                'brand' => 'AOC',
                'price' => 18500,
                'short_description' => '24" 1080p IPS, 165Hz, 1ms, FreeSync Premium',
                'description' => 'Budget-friendly 1080p gaming monitor.',
                'stock' => 30,
            ],

            // Keyboards
            [
                'name' => 'Logitech G Pro X Mechanical Keyboard',
                'sku' => 'LOG-GPROX-KB',
                'category' => 'Keyboard',
                'brand' => 'Logitech',
                'price' => 14500,
                'short_description' => 'Hot-swappable, GX Blue Switches, RGB, TKL',
                'description' => 'Pro-grade mechanical keyboard with hot-swappable switches.',
                'featured' => true,
                'stock' => 20,
                'specs' => [
                    'Basic Information' => [
                        'Switch Type' => 'GX Blue (Clicky)',
                        'Layout' => 'TKL (Tenkeyless)',
                        'Backlight' => 'RGB LIGHTSYNC',
                        'Connection' => 'USB 2.0',
                        'Features' => 'Hot-swappable, Onboard Memory',
                    ],
                ],
            ],
            [
                'name' => 'Keychron K8 Pro Wireless Mechanical Keyboard',
                'sku' => 'KEY-K8PRO',
                'category' => 'Keyboard',
                'brand' => 'Keychron',
                'price' => 11500,
                'short_description' => 'Gateron G Pro Switches, RGB, Wireless, Hot-swappable',
                'description' => 'Versatile wireless mechanical keyboard for Mac and Windows.',
                'stock' => 25,
            ],
            [
                'name' => 'Razer BlackWidow V4 Pro',
                'sku' => 'RAZ-BWV4PRO',
                'category' => 'Keyboard',
                'brand' => 'Razer',
                'price' => 24500,
                'short_description' => 'Razer Green Switches, RGB, Command Dial, Wrist Rest',
                'description' => 'Premium gaming keyboard with macro keys and command dial.',
                'stock' => 15,
            ],
            [
                'name' => 'Redragon K552 Kumara RGB',
                'sku' => 'RED-K552',
                'category' => 'Keyboard',
                'brand' => 'Redragon',
                'price' => 3800,
                'short_description' => 'Outemu Blue Switches, RGB, TKL, Budget Gaming',
                'description' => 'Affordable mechanical gaming keyboard.',
                'stock' => 50,
            ],

            // Mice
            [
                'name' => 'Logitech G Pro X Superlight 2',
                'sku' => 'LOG-GPROXSL2',
                'category' => 'Mouse',
                'brand' => 'Logitech',
                'price' => 18500,
                'short_description' => '60g, HERO 2 Sensor, LIGHTSPEED Wireless, 95 Hours Battery',
                'description' => 'Ultra-lightweight wireless gaming mouse for esports.',
                'featured' => true,
                'stock' => 20,
            ],
            [
                'name' => 'Razer DeathAdder V3 Pro',
                'sku' => 'RAZ-DAV3PRO',
                'category' => 'Mouse',
                'brand' => 'Razer',
                'price' => 16500,
                'short_description' => '63g, Focus Pro 30K Sensor, HyperSpeed Wireless',
                'description' => 'Ergonomic wireless gaming mouse.',
                'stock' => 18,
            ],
            [
                'name' => 'Logitech G502 X Plus',
                'sku' => 'LOG-G502XPLUS',
                'category' => 'Mouse',
                'brand' => 'Logitech',
                'price' => 15500,
                'short_description' => 'HERO 25K Sensor, LIGHTSPEED, RGB, Adjustable Weights',
                'description' => 'Feature-rich wireless gaming mouse.',
                'stock' => 22,
            ],
            [
                'name' => 'SteelSeries Rival 3',
                'sku' => 'SS-RIVAL3',
                'category' => 'Mouse',
                'brand' => 'SteelSeries',
                'price' => 3200,
                'short_description' => 'TrueMove Core Sensor, RGB, 77g',
                'description' => 'Budget-friendly gaming mouse with quality sensor.',
                'stock' => 40,
            ],

            // Headphones
            [
                'name' => 'SteelSeries Arctis Nova Pro Wireless',
                'sku' => 'SS-ARCTISNPW',
                'category' => 'Headphone',
                'brand' => 'SteelSeries',
                'price' => 42000,
                'short_description' => 'Active Noise Cancelling, Dual Wireless, Hi-Res Audio',
                'description' => 'Premium wireless gaming headset with ANC.',
                'featured' => true,
                'stock' => 12,
            ],
            [
                'name' => 'Logitech G Pro X 2 Lightspeed',
                'sku' => 'LOG-GPROX2',
                'category' => 'Headphone',
                'brand' => 'Logitech',
                'price' => 28500,
                'short_description' => '50mm Graphene Drivers, DTS Headphone:X 2.0, Blue VO!CE',
                'description' => 'Pro-grade wireless gaming headset.',
                'stock' => 15,
            ],
            [
                'name' => 'HyperX Cloud III Wireless',
                'sku' => 'HX-CLOUD3W',
                'category' => 'Headphone',
                'brand' => 'HyperX',
                'price' => 15500,
                'short_description' => 'DTS Spatial Audio, 120 Hours Battery, Angled 53mm Drivers',
                'description' => 'Comfortable wireless gaming headset with long battery life.',
                'stock' => 20,
            ],
            [
                'name' => 'Razer BlackShark V2',
                'sku' => 'RAZ-BSV2',
                'category' => 'Headphone',
                'brand' => 'Razer',
                'price' => 8500,
                'short_description' => 'TriForce Titanium Drivers, THX Spatial Audio, HyperClear Cardioid Mic',
                'description' => 'Great value gaming headset.',
                'stock' => 25,
            ],

            // Laptops
            [
                'name' => 'ASUS ROG Strix G16 Gaming Laptop',
                'sku' => 'ASUS-ROGG16-2024',
                'category' => 'Gaming Laptop',
                'brand' => 'ASUS',
                'price' => 185000,
                'compare_price' => 195000,
                'short_description' => 'Intel Core i9-14900HX, RTX 4070, 16" 240Hz, 32GB DDR5, 1TB SSD',
                'description' => 'Powerful gaming laptop with latest Intel and NVIDIA hardware.',
                'featured' => true,
                'stock' => 8,
                'specs' => [
                    'Processor' => [
                        'CPU' => 'Intel Core i9-14900HX',
                        'Cores/Threads' => '24/32',
                        'Max Turbo' => '5.8 GHz',
                    ],
                    'Memory' => [
                        'RAM' => '32GB DDR5-5600',
                        'Max RAM' => '64GB',
                    ],
                    'Storage' => [
                        'SSD' => '1TB PCIe 4.0 NVMe',
                    ],
                    'Graphics' => [
                        'GPU' => 'NVIDIA GeForce RTX 4070 8GB',
                    ],
                    'Display' => [
                        'Screen' => '16" 2560x1600 240Hz',
                        'Panel' => 'IPS, 100% DCI-P3',
                    ],
                ],
            ],
            [
                'name' => 'Lenovo Legion Pro 5 Gaming Laptop',
                'sku' => 'LEN-LEGION5P-2024',
                'category' => 'Gaming Laptop',
                'brand' => 'Lenovo',
                'price' => 165000,
                'short_description' => 'AMD Ryzen 9 7945HX, RTX 4070, 16" 240Hz, 32GB DDR5, 1TB SSD',
                'description' => 'High-performance AMD gaming laptop.',
                'featured' => true,
                'stock' => 10,
            ],
            [
                'name' => 'MSI Katana 15 Gaming Laptop',
                'sku' => 'MSI-KATANA15',
                'category' => 'Gaming Laptop',
                'brand' => 'MSI',
                'price' => 95000,
                'short_description' => 'Intel Core i7-13620H, RTX 4060, 15.6" 144Hz, 16GB DDR5, 512GB SSD',
                'description' => 'Value gaming laptop with RTX 4060.',
                'stock' => 15,
            ],
            [
                'name' => 'Apple MacBook Pro 14" M3 Pro',
                'sku' => 'APL-MBP14-M3P',
                'category' => 'MacBook',
                'brand' => 'Apple',
                'price' => 245000,
                'compare_price' => 255000,
                'short_description' => 'Apple M3 Pro Chip, 18GB RAM, 512GB SSD, 14.2" Liquid Retina XDR',
                'description' => 'Professional laptop with Apple Silicon M3 Pro chip.',
                'featured' => true,
                'stock' => 6,
            ],
            [
                'name' => 'HP Pavilion 15 Laptop',
                'sku' => 'HP-PAV15-2024',
                'category' => 'Student Laptop',
                'brand' => 'HP',
                'price' => 65000,
                'short_description' => 'Intel Core i5-1335U, 16GB RAM, 512GB SSD, 15.6" FHD IPS',
                'description' => 'Affordable laptop for students and everyday use.',
                'stock' => 25,
            ],
            [
                'name' => 'Lenovo IdeaPad Slim 3',
                'sku' => 'LEN-IDEA3-2024',
                'category' => 'Student Laptop',
                'brand' => 'Lenovo',
                'price' => 48000,
                'short_description' => 'AMD Ryzen 5 7520U, 8GB RAM, 512GB SSD, 15.6" FHD',
                'description' => 'Budget-friendly laptop for students.',
                'stock' => 30,
            ],

            // Networking
            [
                'name' => 'TP-Link Archer AX73 WiFi 6 Router',
                'sku' => 'TPL-AX73',
                'category' => 'Router',
                'brand' => 'TP-Link',
                'price' => 12500,
                'short_description' => 'AX5400 WiFi 6, Dual Band, 6 Antennas, USB 3.0',
                'description' => 'High-performance WiFi 6 router for home use.',
                'stock' => 20,
            ],
            [
                'name' => 'ASUS RT-AX86U Pro Gaming Router',
                'sku' => 'ASUS-AX86UPRO',
                'category' => 'Router',
                'brand' => 'ASUS',
                'price' => 28500,
                'short_description' => 'AX5700 WiFi 6, 2.5G Port, Game Acceleration',
                'description' => 'Gaming-focused router with priority traffic.',
                'stock' => 12,
            ],
            [
                'name' => 'TP-Link Deco XE75 Pro Mesh System (3-Pack)',
                'sku' => 'TPL-XE75PRO-3PK',
                'category' => 'Router',
                'brand' => 'TP-Link',
                'price' => 42000,
                'short_description' => 'WiFi 6E AXE5400, Tri-Band, AI-Driven Mesh',
                'description' => 'Whole-home WiFi 6E mesh system.',
                'stock' => 10,
            ],

            // UPS
            [
                'name' => 'APC Back-UPS Pro 1500VA',
                'sku' => 'APC-BR1500G',
                'category' => 'UPS',
                'brand' => 'APC',
                'price' => 22500,
                'short_description' => '1500VA/865W, LCD, AVR, USB, 10 Outlets',
                'description' => 'Professional UPS for PC and network equipment.',
                'stock' => 15,
            ],
            [
                'name' => 'CyberPower CP1500EPFCLCD',
                'sku' => 'CYB-CP1500EP',
                'category' => 'UPS',
                'brand' => 'CyberPower',
                'price' => 18500,
                'short_description' => '1500VA/900W, Pure Sine Wave, LCD',
                'description' => 'Pure sine wave UPS for sensitive equipment.',
                'stock' => 18,
            ],
            [
                'name' => 'APC Easy UPS 1000VA',
                'sku' => 'APC-BVX1000',
                'category' => 'UPS',
                'brand' => 'APC',
                'price' => 8500,
                'short_description' => '1000VA/600W, AVR, 4 Outlets',
                'description' => 'Basic UPS for home PCs.',
                'stock' => 30,
            ],

            // Gaming Chairs
            [
                'name' => 'Secretlab Titan Evo 2024 Series',
                'sku' => 'SEC-TITAN-2024',
                'category' => 'Gaming Chair',
                'brand' => 'Secretlab',
                'price' => 48000,
                'short_description' => '4-Way L-ADAPT Lumbar, Magnetic Armrests, Neo Hybrid Leatherette',
                'description' => 'Premium ergonomic gaming chair.',
                'featured' => true,
                'stock' => 10,
            ],
            [
                'name' => 'Cougar Armor Titan Pro',
                'sku' => 'COU-ATITANPRO',
                'category' => 'Gaming Chair',
                'brand' => 'Cougar',
                'price' => 32000,
                'short_description' => 'Suede-Like Texture, 160° Recline, 4D Armrests',
                'description' => 'High-quality gaming chair with premium materials.',
                'stock' => 12,
            ],
            [
                'name' => 'DXRacer King Series',
                'sku' => 'DXR-KING',
                'category' => 'Gaming Chair',
                'brand' => 'DXRacer',
                'price' => 38000,
                'short_description' => 'Extra Wide Seat, PU Leather, 4D Armrests',
                'description' => 'Comfortable gaming chair for larger users.',
                'stock' => 8,
            ],

            // Power Supplies
            [
                'name' => 'Corsair RM1000x 1000W 80+ Gold',
                'sku' => 'COR-RM1000X',
                'category' => 'Power Supply',
                'brand' => 'Corsair',
                'price' => 18500,
                'short_description' => '1000W, 80+ Gold, Fully Modular, Zero RPM Mode',
                'description' => 'High-capacity PSU for high-end builds.',
                'featured' => true,
                'stock' => 15,
            ],
            [
                'name' => 'Seasonic Focus GX-850 850W 80+ Gold',
                'sku' => 'SEA-GX850',
                'category' => 'Power Supply',
                'brand' => 'Seasonic',
                'price' => 13500,
                'short_description' => '850W, 80+ Gold, Fully Modular, 10-Year Warranty',
                'description' => 'Reliable PSU with excellent warranty.',
                'stock' => 20,
            ],
            [
                'name' => 'Cooler Master MWE Gold 750W V2',
                'sku' => 'CM-MWE750V2',
                'category' => 'Power Supply',
                'brand' => 'Cooler Master',
                'price' => 9500,
                'short_description' => '750W, 80+ Gold, Semi-Modular',
                'description' => 'Good value PSU for mid-range builds.',
                'stock' => 25,
            ],
            [
                'name' => 'DeepCool PK650D 650W 80+ Bronze',
                'sku' => 'DC-PK650D',
                'category' => 'Power Supply',
                'brand' => 'DeepCool',
                'price' => 5500,
                'short_description' => '650W, 80+ Bronze, Non-Modular',
                'description' => 'Budget PSU for entry-level builds.',
                'stock' => 35,
            ],

            // Cases
            [
                'name' => 'Lian Li O11 Dynamic EVO',
                'sku' => 'LL-O11DEVO',
                'category' => 'Casing',
                'brand' => 'Lian Li',
                'price' => 18500,
                'short_description' => 'Mid-Tower, Dual Chamber, Tempered Glass, USB-C',
                'description' => 'Premium PC case with excellent airflow.',
                'featured' => true,
                'stock' => 15,
            ],
            [
                'name' => 'NZXT H7 Flow',
                'sku' => 'NZXT-H7FLOW',
                'category' => 'Casing',
                'brand' => 'NZXT',
                'price' => 13500,
                'short_description' => 'Mid-Tower, High Airflow, Tempered Glass',
                'description' => 'Clean design with high airflow front panel.',
                'stock' => 18,
            ],
            [
                'name' => 'Corsair 4000D Airflow',
                'sku' => 'COR-4000DAF',
                'category' => 'Casing',
                'brand' => 'Corsair',
                'price' => 9500,
                'short_description' => 'Mid-Tower, High Airflow, Tempered Glass',
                'description' => 'Popular mid-tower with great airflow.',
                'stock' => 25,
            ],
            [
                'name' => 'DeepCool CH510',
                'sku' => 'DC-CH510',
                'category' => 'Casing',
                'brand' => 'DeepCool',
                'price' => 5500,
                'short_description' => 'Mid-Tower, Tempered Glass, Mesh Front',
                'description' => 'Budget-friendly case with good airflow.',
                'stock' => 30,
            ],

            // Coolers
            [
                'name' => 'Noctua NH-D15 chromax.black',
                'sku' => 'NOC-NHD15-BLK',
                'category' => 'CPU Cooler',
                'brand' => 'be quiet!',
                'price' => 12500,
                'short_description' => 'Dual Tower, 6 Heatpipes, 2x NF-A15 Fans',
                'description' => 'Best air cooler for high-end CPUs.',
                'stock' => 15,
            ],
            [
                'name' => 'Corsair iCUE H150i Elite LCD XT',
                'sku' => 'COR-H150I-LCD',
                'category' => 'CPU Cooler',
                'brand' => 'Corsair',
                'price' => 28500,
                'short_description' => '360mm AIO, LCD Pump Head, RGB',
                'description' => 'Premium AIO liquid cooler with LCD display.',
                'stock' => 12,
            ],
            [
                'name' => 'DeepCool AK620',
                'sku' => 'DC-AK620',
                'category' => 'CPU Cooler',
                'brand' => 'DeepCool',
                'price' => 6500,
                'short_description' => 'Dual Tower, 6 Heatpipes, 2x FK120 Fans',
                'description' => 'Great value dual tower cooler.',
                'stock' => 20,
            ],
            [
                'name' => 'Cooler Master Hyper 212 Halo',
                'sku' => 'CM-212HALO',
                'category' => 'CPU Cooler',
                'brand' => 'Cooler Master',
                'price' => 4500,
                'short_description' => 'Tower Cooler, 4 Heatpipes, ARGB Fan',
                'description' => 'Popular budget tower cooler with RGB.',
                'stock' => 30,
            ],
        ];
    }
}
