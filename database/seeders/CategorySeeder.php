<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Main Categories
            [
                'name' => 'Desktop',
                'description' => 'Complete desktop computers and workstations',
                'icon' => 'desktop',
                'children' => [
                    ['name' => 'Brand PC', 'description' => 'Pre-built branded desktop computers'],
                    ['name' => 'Star PC', 'description' => 'Custom built PC configurations'],
                    ['name' => 'Gaming PC', 'description' => 'High-performance gaming desktops'],
                    ['name' => 'All-in-One PC', 'description' => 'Integrated display and computer'],
                    ['name' => 'Apple Mac', 'description' => 'Apple desktop computers'],
                ],
            ],
            [
                'name' => 'Laptop',
                'description' => 'Portable computers and notebooks',
                'icon' => 'laptop',
                'children' => [
                    ['name' => 'Gaming Laptop', 'description' => 'High-performance gaming laptops'],
                    ['name' => 'Ultrabook', 'description' => 'Thin and light laptops'],
                    ['name' => 'Business Laptop', 'description' => 'Professional business laptops'],
                    ['name' => 'Student Laptop', 'description' => 'Budget-friendly laptops for students'],
                    ['name' => 'MacBook', 'description' => 'Apple MacBook series'],
                ],
            ],
            [
                'name' => 'Component',
                'description' => 'Computer components and parts',
                'icon' => 'cpu',
                'children' => [
                    ['name' => 'Processor', 'description' => 'CPU processors from Intel and AMD'],
                    ['name' => 'Motherboard', 'description' => 'Motherboards for all platforms'],
                    ['name' => 'RAM', 'description' => 'Desktop and laptop memory'],
                    ['name' => 'Graphics Card', 'description' => 'GPU and graphics cards'],
                    ['name' => 'Power Supply', 'description' => 'PSU and power supplies'],
                    ['name' => 'Casing', 'description' => 'PC cases and cabinets'],
                    ['name' => 'CPU Cooler', 'description' => 'Air and liquid CPU coolers'],
                ],
            ],
            [
                'name' => 'Monitor',
                'description' => 'Display monitors for all purposes',
                'icon' => 'monitor',
                'children' => [
                    ['name' => 'Gaming Monitor', 'description' => 'High refresh rate gaming monitors'],
                    ['name' => 'Professional Monitor', 'description' => 'Color-accurate professional displays'],
                    ['name' => 'Curved Monitor', 'description' => 'Immersive curved displays'],
                    ['name' => '4K Monitor', 'description' => 'Ultra HD 4K resolution monitors'],
                    ['name' => 'Budget Monitor', 'description' => 'Affordable everyday monitors'],
                ],
            ],
            [
                'name' => 'Storage',
                'description' => 'Storage devices and solutions',
                'icon' => 'hard-drive',
                'children' => [
                    ['name' => 'SSD', 'description' => 'Solid State Drives'],
                    ['name' => 'HDD', 'description' => 'Hard Disk Drives'],
                    ['name' => 'NVMe SSD', 'description' => 'High-speed NVMe drives'],
                    ['name' => 'External HDD', 'description' => 'Portable external drives'],
                    ['name' => 'Pen Drive', 'description' => 'USB flash drives'],
                ],
            ],
            [
                'name' => 'Peripherals',
                'description' => 'Computer peripherals and accessories',
                'icon' => 'mouse',
                'children' => [
                    ['name' => 'Keyboard', 'description' => 'Mechanical and membrane keyboards'],
                    ['name' => 'Mouse', 'description' => 'Gaming and office mice'],
                    ['name' => 'Headphone', 'description' => 'Headphones and headsets'],
                    ['name' => 'Webcam', 'description' => 'Web cameras for streaming'],
                    ['name' => 'Speaker', 'description' => 'Computer speakers'],
                    ['name' => 'Mousepad', 'description' => 'Gaming and office mousepads'],
                ],
            ],
            [
                'name' => 'Networking',
                'description' => 'Networking equipment and accessories',
                'icon' => 'wifi',
                'children' => [
                    ['name' => 'Router', 'description' => 'WiFi routers'],
                    ['name' => 'Switch', 'description' => 'Network switches'],
                    ['name' => 'Access Point', 'description' => 'Wireless access points'],
                    ['name' => 'Network Card', 'description' => 'WiFi and ethernet cards'],
                    ['name' => 'Network Cable', 'description' => 'Ethernet and network cables'],
                ],
            ],
            [
                'name' => 'Office Equipment',
                'description' => 'Office automation products',
                'icon' => 'printer',
                'children' => [
                    ['name' => 'Printer', 'description' => 'Inkjet and laser printers'],
                    ['name' => 'Scanner', 'description' => 'Document scanners'],
                    ['name' => 'Projector', 'description' => 'Projectors for presentations'],
                    ['name' => 'UPS', 'description' => 'Uninterruptible power supplies'],
                    ['name' => 'Photocopier', 'description' => 'Copy machines'],
                ],
            ],
            [
                'name' => 'Gaming',
                'description' => 'Gaming accessories and consoles',
                'icon' => 'gamepad',
                'children' => [
                    ['name' => 'Gaming Chair', 'description' => 'Ergonomic gaming chairs'],
                    ['name' => 'Gaming Desk', 'description' => 'Gaming desks and tables'],
                    ['name' => 'Gaming Controller', 'description' => 'Game controllers and joysticks'],
                    ['name' => 'Gaming Console', 'description' => 'PlayStation, Xbox, Nintendo'],
                    ['name' => 'VR Headset', 'description' => 'Virtual reality headsets'],
                ],
            ],
            [
                'name' => 'Software',
                'description' => 'Software and licenses',
                'icon' => 'code',
                'children' => [
                    ['name' => 'Operating System', 'description' => 'Windows, macOS licenses'],
                    ['name' => 'Office Suite', 'description' => 'Microsoft Office, productivity'],
                    ['name' => 'Antivirus', 'description' => 'Security software'],
                    ['name' => 'Graphics Software', 'description' => 'Adobe, design software'],
                ],
            ],
        ];

        $sortOrder = 0;
        foreach ($categories as $categoryData) {
            $parent = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'icon' => $categoryData['icon'] ?? null,
                'parent_id' => null,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'is_featured' => in_array($categoryData['name'], ['Desktop', 'Laptop', 'Component', 'Monitor']),
            ]);

            if (!empty($categoryData['children'])) {
                $childOrder = 0;
                foreach ($categoryData['children'] as $child) {
                    Category::create([
                        'name' => $child['name'],
                        'slug' => Str::slug($child['name']),
                        'description' => $child['description'],
                        'parent_id' => $parent->id,
                        'sort_order' => $childOrder++,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Categories seeded: ' . Category::count() . ' total');
    }
}
