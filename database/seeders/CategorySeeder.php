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
            // Laptop
            [
                'name' => 'Laptop',
                'description' => 'Portable computers, notebooks and ultrabooks',
                'icon' => 'laptop',
                'children' => [
                    ['name' => 'All Laptop', 'description' => 'All types of laptops'],
                    ['name' => 'Gaming Laptop', 'description' => 'High-performance gaming laptops'],
                    ['name' => 'Ultrabook', 'description' => 'Thin and light laptops'],
                    ['name' => 'Business Laptop', 'description' => 'Professional business laptops'],
                    ['name' => 'Student Laptop', 'description' => 'Budget-friendly laptops for students'],
                    ['name' => 'MacBook', 'description' => 'Apple MacBook series'],
                    ['name' => 'Laptop Accessories', 'description' => 'Laptop related accessories', 'children' => [
                        ['name' => 'Laptop RAM', 'description' => 'Memory upgrades for laptops'],
                        ['name' => 'Laptop Cooler', 'description' => 'Cooling pads for laptops'],
                        ['name' => 'Laptop Caddy', 'description' => 'HDD/SSD caddy for laptops'],
                        ['name' => 'Laptop Bag', 'description' => 'Bags and cases for laptops'],
                        ['name' => 'Laptop Stand', 'description' => 'Stands and risers for laptops'],
                        ['name' => 'Laptop Battery', 'description' => 'Replacement batteries'],
                        ['name' => 'Laptop Adapter', 'description' => 'Power adapters and chargers'],
                    ]],
                ],
            ],

            // Desktop and Server
            [
                'name' => 'Desktop',
                'description' => 'Desktop computers and workstations',
                'icon' => 'desktop',
                'children' => [
                    ['name' => 'Brand PC', 'description' => 'Pre-built branded desktop computers'],
                    ['name' => 'All-in-One PC', 'description' => 'Integrated display and computer'],
                    ['name' => 'Mini PC', 'description' => 'Compact desktop computers'],
                    ['name' => 'Gaming PC', 'description' => 'High-performance gaming desktops'],
                    ['name' => 'Workstation PC', 'description' => 'Professional workstations'],
                    ['name' => 'AI Workstation PC', 'description' => 'AI and ML workstations'],
                ],
            ],

            // Desktop Component
            [
                'name' => 'Component',
                'description' => 'Computer components and parts',
                'icon' => 'cpu',
                'children' => [
                    ['name' => 'Processor', 'description' => 'CPU processors from Intel and AMD'],
                    ['name' => 'Motherboard', 'description' => 'Motherboards for all platforms'],
                    ['name' => 'Desktop RAM', 'description' => 'Desktop memory modules'],
                    ['name' => 'Graphics Card', 'description' => 'GPU and graphics cards'],
                    ['name' => 'Power Supply', 'description' => 'PSU and power supplies'],
                    ['name' => 'Casing', 'description' => 'PC cases and cabinets'],
                    ['name' => 'Casing Fan', 'description' => 'Case cooling fans'],
                    ['name' => 'CPU Cooler', 'description' => 'Air and liquid CPU coolers'],
                    ['name' => 'Thermal Paste', 'description' => 'Thermal compounds and pastes'],
                    ['name' => 'LED Strip', 'description' => 'RGB LED lighting strips'],
                    ['name' => 'Graphics Card Holder', 'description' => 'GPU support brackets'],
                ],
            ],

            // Storage
            [
                'name' => 'Storage',
                'description' => 'Storage devices and solutions',
                'icon' => 'hard-drive',
                'children' => [
                    ['name' => 'Internal HDD', 'description' => 'Internal hard disk drives'],
                    ['name' => 'Internal SSD', 'description' => 'SATA solid state drives'],
                    ['name' => 'NVMe SSD', 'description' => 'High-speed NVMe M.2 drives'],
                    ['name' => 'External HDD', 'description' => 'Portable external hard drives'],
                    ['name' => 'External SSD', 'description' => 'Portable external SSDs'],
                    ['name' => 'Pen Drive', 'description' => 'USB flash drives'],
                    ['name' => 'Memory Card', 'description' => 'SD and microSD cards'],
                    ['name' => 'HDD & SSD Case', 'description' => 'Enclosures for drives'],
                    ['name' => 'Card Reader', 'description' => 'Memory card readers'],
                ],
            ],

            // Monitor
            [
                'name' => 'Monitor',
                'description' => 'Display monitors for all purposes',
                'icon' => 'monitor',
                'children' => [
                    ['name' => 'Gaming Monitor', 'description' => 'High refresh rate gaming monitors'],
                    ['name' => 'Professional Monitor', 'description' => 'Color-accurate professional displays'],
                    ['name' => 'Curved Monitor', 'description' => 'Immersive curved displays'],
                    ['name' => '4K Monitor', 'description' => 'Ultra HD 4K resolution monitors'],
                    ['name' => 'Ultrawide Monitor', 'description' => '21:9 ultrawide displays'],
                    ['name' => 'Portable Monitor', 'description' => 'Portable secondary displays'],
                    ['name' => 'Monitor Accessories', 'description' => 'Monitor stands and accessories', 'children' => [
                        ['name' => 'Monitor Mount', 'description' => 'Monitor arms and mounts'],
                        ['name' => 'Monitor Stand', 'description' => 'Desktop monitor stands'],
                    ]],
                ],
            ],

            // Peripherals - Keyboard & Mouse
            [
                'name' => 'Keyboard & Mouse',
                'description' => 'Input devices',
                'icon' => 'keyboard',
                'children' => [
                    ['name' => 'Keyboard', 'description' => 'Mechanical and membrane keyboards'],
                    ['name' => 'Gaming Keyboard', 'description' => 'Gaming mechanical keyboards'],
                    ['name' => 'Mouse', 'description' => 'Optical and laser mice'],
                    ['name' => 'Gaming Mouse', 'description' => 'Gaming mice'],
                    ['name' => 'Keyboard Mouse Combo', 'description' => 'Keyboard and mouse bundles'],
                    ['name' => 'Mouse Pad', 'description' => 'Gaming and office mousepads'],
                ],
            ],

            // UPS & Power
            [
                'name' => 'UPS',
                'description' => 'Uninterruptible power supplies',
                'icon' => 'battery',
                'children' => [
                    ['name' => 'Offline UPS', 'description' => 'Standby UPS systems'],
                    ['name' => 'Online UPS', 'description' => 'Double-conversion UPS'],
                    ['name' => 'Line Interactive UPS', 'description' => 'Line interactive UPS'],
                    ['name' => 'UPS Battery', 'description' => 'Replacement UPS batteries'],
                    ['name' => 'Power Station', 'description' => 'Portable power stations'],
                ],
            ],

            // Gaming
            [
                'name' => 'Gaming',
                'description' => 'Gaming accessories and consoles',
                'icon' => 'gamepad',
                'children' => [
                    ['name' => 'Gaming Console', 'description' => 'PlayStation, Xbox, Nintendo'],
                    ['name' => 'Gaming Controller', 'description' => 'Game controllers and joysticks'],
                    ['name' => 'VR Headset', 'description' => 'Virtual reality headsets'],
                    ['name' => 'Gaming Chair', 'description' => 'Ergonomic gaming chairs'],
                    ['name' => 'Gaming Desk', 'description' => 'Gaming desks and tables'],
                    ['name' => 'Gaming Sofa', 'description' => 'Gaming sofas and bean bags'],
                    ['name' => 'Games', 'description' => 'Video games and titles'],
                    ['name' => 'Game Streaming', 'description' => 'Streaming equipment'],
                ],
            ],

            // Tablet PC
            [
                'name' => 'Tablet',
                'description' => 'Tablet PCs and accessories',
                'icon' => 'tablet',
                'children' => [
                    ['name' => 'Android Tablet', 'description' => 'Android tablets'],
                    ['name' => 'iPad', 'description' => 'Apple iPads'],
                    ['name' => 'Windows Tablet', 'description' => 'Windows tablets'],
                    ['name' => 'Graphics Tablet', 'description' => 'Drawing tablets', 'children' => [
                        ['name' => 'Pen Tablet', 'description' => 'Pen drawing tablets'],
                        ['name' => 'Pen Display', 'description' => 'Display drawing tablets'],
                    ]],
                    ['name' => 'Digital Signature Pad', 'description' => 'Electronic signature pads'],
                    ['name' => 'Stylus Pen', 'description' => 'Stylus pens for tablets'],
                    ['name' => 'Tablet Accessories', 'description' => 'Tablet covers and accessories'],
                ],
            ],

            // Printer
            [
                'name' => 'Printer',
                'description' => 'Printers and printing solutions',
                'icon' => 'printer',
                'children' => [
                    ['name' => 'Laser Printer', 'description' => 'Laser and multifunction printers'],
                    ['name' => 'Ink Tank Printer', 'description' => 'Continuous ink supply printers'],
                    ['name' => 'Inkjet Printer', 'description' => 'Inkjet printers'],
                    ['name' => 'Dot Matrix Printer', 'description' => 'Impact printers'],
                    ['name' => 'Label Printer', 'description' => 'Label and barcode printers'],
                    ['name' => 'Card Printer', 'description' => 'ID card printers'],
                    ['name' => 'POS Printer', 'description' => 'Receipt and POS printers'],
                    ['name' => 'Large Format Printer', 'description' => 'Wide format printers'],
                    ['name' => 'Photo Printer', 'description' => 'Photo printing solutions'],
                    ['name' => 'Printer Paper', 'description' => 'Printing papers'],
                    ['name' => 'Toner & Cartridge', 'description' => 'Printer consumables', 'children' => [
                        ['name' => 'Toner', 'description' => 'Laser printer toners'],
                        ['name' => 'Ink Cartridge', 'description' => 'Inkjet cartridges'],
                        ['name' => 'Ribbon', 'description' => 'Printer ribbons'],
                        ['name' => 'Drum Unit', 'description' => 'Printer drum units'],
                    ]],
                ],
            ],

            // Camera
            [
                'name' => 'Camera',
                'description' => 'Cameras and photography equipment',
                'icon' => 'camera',
                'children' => [
                    ['name' => 'DSLR Camera', 'description' => 'Digital SLR cameras'],
                    ['name' => 'Mirrorless Camera', 'description' => 'Mirrorless interchangeable lens cameras'],
                    ['name' => 'Compact Camera', 'description' => 'Point and shoot cameras'],
                    ['name' => 'Action Camera', 'description' => 'Action and sports cameras'],
                    ['name' => 'Video Camera', 'description' => 'Camcorders and video cameras'],
                    ['name' => 'Webcam', 'description' => 'Web cameras for streaming and video calls'],
                    ['name' => 'Video Conferencing', 'description' => 'Conference cameras'],
                    ['name' => 'Drone', 'description' => 'Camera drones'],
                    ['name' => 'Gimbal', 'description' => 'Camera stabilizers'],
                    ['name' => 'Camera Lens', 'description' => 'Interchangeable lenses'],
                    ['name' => 'Camera Accessories', 'description' => 'Camera bags, tripods, etc.'],
                    ['name' => 'Studio Equipment', 'description' => 'Studio lighting and equipment', 'children' => [
                        ['name' => 'Tripod', 'description' => 'Camera tripods and monopods'],
                        ['name' => 'Flash & Ring Light', 'description' => 'Camera flashes and lights'],
                        ['name' => 'Studio Microphone', 'description' => 'Recording microphones'],
                        ['name' => 'Audio Interface', 'description' => 'Audio recording interfaces'],
                        ['name' => 'Mixer', 'description' => 'Audio mixers'],
                    ]],
                ],
            ],

            // Security
            [
                'name' => 'Security',
                'description' => 'Security and surveillance systems',
                'icon' => 'shield',
                'children' => [
                    ['name' => 'CC Camera', 'description' => 'Analog CCTV cameras'],
                    ['name' => 'IP Camera', 'description' => 'Network IP cameras'],
                    ['name' => 'WiFi Camera', 'description' => 'Wireless security cameras'],
                    ['name' => 'DVR', 'description' => 'Digital video recorders'],
                    ['name' => 'NVR', 'description' => 'Network video recorders'],
                    ['name' => 'XVR', 'description' => 'Hybrid video recorders'],
                    ['name' => 'CCTV Package', 'description' => 'Complete CCTV kits'],
                    ['name' => 'CC Camera Accessories', 'description' => 'CCTV accessories'],
                    ['name' => 'Smart Home', 'description' => 'Smart home security', 'children' => [
                        ['name' => 'Smart Lock', 'description' => 'Smart door locks'],
                        ['name' => 'Smart Door Bell', 'description' => 'Video doorbells'],
                        ['name' => 'Smart Home Hub', 'description' => 'Smart home controllers'],
                    ]],
                    ['name' => 'Access Control', 'description' => 'Access control systems'],
                    ['name' => 'Time Attendance', 'description' => 'Time attendance machines'],
                ],
            ],

            // Network
            [
                'name' => 'Network',
                'description' => 'Networking equipment and accessories',
                'icon' => 'wifi',
                'children' => [
                    ['name' => 'Router', 'description' => 'WiFi routers'],
                    ['name' => 'Gaming Router', 'description' => 'High-performance gaming routers'],
                    ['name' => 'Access Point', 'description' => 'Wireless access points'],
                    ['name' => 'Range Extender', 'description' => 'WiFi range extenders'],
                    ['name' => 'Network Switch', 'description' => 'Managed and unmanaged switches'],
                    ['name' => 'LAN Card', 'description' => 'Network interface cards'],
                    ['name' => 'WiFi Adapter', 'description' => 'USB WiFi adapters'],
                    ['name' => 'Network Cable', 'description' => 'Ethernet and fiber cables'],
                    ['name' => 'OLT', 'description' => 'Optical line terminals'],
                    ['name' => 'ONU', 'description' => 'Optical network units'],
                    ['name' => 'Network Storage (NAS)', 'description' => 'Network attached storage'],
                    ['name' => 'Modem', 'description' => 'DSL and cable modems'],
                    ['name' => 'Satellite Internet', 'description' => 'Satellite internet equipment', 'children' => [
                        ['name' => 'Starlink', 'description' => 'Starlink satellite internet'],
                    ]],
                    ['name' => 'Network Accessories', 'description' => 'Connectors, tools, etc.', 'children' => [
                        ['name' => 'Connector', 'description' => 'RJ45 and fiber connectors'],
                        ['name' => 'Patch Panel', 'description' => 'Network patch panels'],
                        ['name' => 'Crimping Tool', 'description' => 'Network crimping tools'],
                        ['name' => 'Network Rack', 'description' => 'Server and network racks'],
                    ]],
                ],
            ],

            // Sound
            [
                'name' => 'Sound',
                'description' => 'Audio and sound systems',
                'icon' => 'speaker',
                'children' => [
                    ['name' => 'Speaker', 'description' => 'Computer and multimedia speakers'],
                    ['name' => 'Bluetooth Speaker', 'description' => 'Portable bluetooth speakers'],
                    ['name' => 'Soundbar', 'description' => 'TV soundbars'],
                    ['name' => 'Home Theater', 'description' => 'Home theater systems'],
                    ['name' => 'PA System', 'description' => 'Public address systems'],
                    ['name' => 'Amplifier', 'description' => 'Audio amplifiers'],
                    ['name' => 'Headphone', 'description' => 'Over-ear and on-ear headphones'],
                    ['name' => 'Gaming Headphone', 'description' => 'Gaming headsets'],
                    ['name' => 'Earphone', 'description' => 'In-ear monitors'],
                    ['name' => 'Earbuds', 'description' => 'True wireless earbuds'],
                    ['name' => 'Neckband', 'description' => 'Wireless neckband earphones'],
                    ['name' => 'Microphone', 'description' => 'Vocal and instrument microphones'],
                    ['name' => 'Voice Recorder', 'description' => 'Digital voice recorders'],
                    ['name' => 'Music Player', 'description' => 'MP3 and digital audio players'],
                    ['name' => 'Musical Instrument', 'description' => 'Digital musical instruments'],
                ],
            ],

            // Office Equipment
            [
                'name' => 'Office Equipment',
                'description' => 'Office automation products',
                'icon' => 'briefcase',
                'children' => [
                    ['name' => 'Photocopier', 'description' => 'Copy machines'],
                    ['name' => 'Scanner', 'description' => 'Document scanners', 'children' => [
                        ['name' => 'Flatbed Scanner', 'description' => 'Flatbed document scanners'],
                        ['name' => 'Sheet-fed Scanner', 'description' => 'Automatic document feeders'],
                        ['name' => 'Book Scanner', 'description' => 'Book and document scanners'],
                        ['name' => 'Large Format Scanner', 'description' => 'Wide format scanners'],
                    ]],
                    ['name' => 'Projector', 'description' => 'Projectors for presentations'],
                    ['name' => 'Projector Screen', 'description' => 'Projection screens'],
                    ['name' => 'Projector Accessories', 'description' => 'Mounts and accessories'],
                    ['name' => 'Interactive Panel', 'description' => 'Interactive displays'],
                    ['name' => 'Digital Signage', 'description' => 'Digital display signage'],
                    ['name' => 'Conference System', 'description' => 'Video conferencing systems'],
                    ['name' => 'Presenter', 'description' => 'Presentation remotes'],
                    ['name' => 'POS System', 'description' => 'Point of sale systems', 'children' => [
                        ['name' => 'POS Terminal', 'description' => 'POS terminals and machines'],
                        ['name' => 'Barcode Scanner', 'description' => 'Barcode and QR scanners'],
                        ['name' => 'Cash Register', 'description' => 'Cash registers and drawers'],
                        ['name' => 'Weighing Scale', 'description' => 'Digital weighing scales'],
                    ]],
                    ['name' => 'Telephone', 'description' => 'Telephone sets', 'children' => [
                        ['name' => 'Land Phone', 'description' => 'Corded and cordless phones'],
                        ['name' => 'IP Phone', 'description' => 'VoIP phones'],
                    ]],
                    ['name' => 'Money Counting Machine', 'description' => 'Cash counting machines'],
                    ['name' => 'Laminating Machine', 'description' => 'Laminating and binding machines'],
                    ['name' => 'Paper Shredder', 'description' => 'Document shredders'],
                    ['name' => 'Safe Box', 'description' => 'Safes and lockers'],
                    ['name' => 'Calculator', 'description' => 'Electronic calculators'],
                ],
            ],

            // Accessories
            [
                'name' => 'Accessories',
                'description' => 'Computer and electronic accessories',
                'icon' => 'plug',
                'children' => [
                    ['name' => 'Cable & Converter', 'description' => 'Cables and adapters', 'children' => [
                        ['name' => 'HDMI Cable', 'description' => 'HDMI cables and adapters'],
                        ['name' => 'USB Cable', 'description' => 'USB cables'],
                        ['name' => 'USB Hub', 'description' => 'USB hubs and docks'],
                        ['name' => 'Type-C Hub', 'description' => 'USB-C docking stations'],
                        ['name' => 'Display Adapter', 'description' => 'Display port adapters'],
                        ['name' => 'Audio Cable', 'description' => 'Audio cables'],
                        ['name' => 'KVM Switch', 'description' => 'KVM switches'],
                    ]],
                    ['name' => 'Bluetooth Adapter', 'description' => 'Bluetooth dongles'],
                    ['name' => 'Power Strip', 'description' => 'Surge protectors and power strips'],
                    ['name' => 'Drycell Battery', 'description' => 'AA, AAA batteries'],
                    ['name' => 'Signature Pad', 'description' => 'Electronic signature pads'],
                    ['name' => 'Power Bank', 'description' => 'Portable chargers'],
                    ['name' => 'Car Charger', 'description' => 'Car charging adapters'],
                    ['name' => 'Wireless Charger', 'description' => 'Qi wireless chargers'],
                    ['name' => 'Wall Charger', 'description' => 'Fast charging adapters'],
                    ['name' => 'Phone Holder', 'description' => 'Phone stands and holders'],
                    ['name' => 'Bag', 'description' => 'Laptop and camera bags'],
                ],
            ],

            // Software
            [
                'name' => 'Software',
                'description' => 'Software and licenses',
                'icon' => 'code',
                'children' => [
                    ['name' => 'Antivirus', 'description' => 'Security software', 'children' => [
                        ['name' => 'Kaspersky', 'description' => 'Kaspersky antivirus'],
                        ['name' => 'Bitdefender', 'description' => 'Bitdefender antivirus'],
                        ['name' => 'ESET', 'description' => 'ESET antivirus'],
                        ['name' => 'Norton', 'description' => 'Norton security'],
                    ]],
                    ['name' => 'Office Application', 'description' => 'Productivity software', 'children' => [
                        ['name' => 'Microsoft Office', 'description' => 'MS Office suite'],
                        ['name' => 'Adobe Creative Cloud', 'description' => 'Adobe applications'],
                    ]],
                    ['name' => 'Operating System', 'description' => 'Windows, macOS licenses'],
                    ['name' => 'Graphics Software', 'description' => 'Design and editing software'],
                    ['name' => 'Engineering Software', 'description' => 'CAD and engineering tools'],
                    ['name' => 'Remote Management', 'description' => 'Remote desktop software'],
                ],
            ],

            // Gadget
            [
                'name' => 'Gadget',
                'description' => 'Smart gadgets and wearables',
                'icon' => 'watch',
                'children' => [
                    ['name' => 'Smartwatch', 'description' => 'Smart watches and fitness bands'],
                    ['name' => 'Fitness Tracker', 'description' => 'Fitness bands and trackers'],
                    ['name' => 'Smart Band', 'description' => 'Smart fitness bands'],
                ],
            ],

            // Mobile Phone
            [
                'name' => 'Mobile Phone',
                'description' => 'Smartphones and feature phones',
                'icon' => 'smartphone',
                'children' => [
                    ['name' => 'Smartphone', 'description' => 'Android and iOS smartphones'],
                    ['name' => 'Feature Phone', 'description' => 'Basic mobile phones'],
                    ['name' => 'Mobile Accessories', 'description' => 'Phone accessories', 'children' => [
                        ['name' => 'Phone Case', 'description' => 'Mobile phone cases'],
                        ['name' => 'Screen Protector', 'description' => 'Tempered glass protectors'],
                        ['name' => 'Mobile Charger', 'description' => 'Phone chargers'],
                        ['name' => 'Mobile Cable', 'description' => 'Charging cables'],
                    ]],
                ],
            ],

            // Appliances
            [
                'name' => 'Appliances',
                'description' => 'Home and kitchen appliances',
                'icon' => 'home',
                'children' => [
                    ['name' => 'Television', 'description' => 'Smart TVs and displays', 'children' => [
                        ['name' => 'Smart TV', 'description' => 'Smart LED/OLED TVs'],
                        ['name' => 'Android TV', 'description' => 'Android smart TVs'],
                        ['name' => 'TV Stick', 'description' => 'Streaming devices'],
                        ['name' => 'TV Accessories', 'description' => 'TV mounts and accessories'],
                    ]],
                    ['name' => 'Refrigerator', 'description' => 'Refrigerators and freezers'],
                    ['name' => 'Air Conditioner', 'description' => 'AC and cooling', 'children' => [
                        ['name' => 'Split AC', 'description' => 'Split air conditioners'],
                        ['name' => 'Cassette AC', 'description' => 'Cassette air conditioners'],
                        ['name' => 'Air Purifier', 'description' => 'Air purifiers'],
                        ['name' => 'Air Cooler', 'description' => 'Evaporative air coolers'],
                    ]],
                    ['name' => 'Kitchen Appliances', 'description' => 'Kitchen equipment', 'children' => [
                        ['name' => 'Microwave Oven', 'description' => 'Microwave and convection ovens'],
                        ['name' => 'Electric Oven', 'description' => 'Electric ovens'],
                        ['name' => 'Blender', 'description' => 'Blenders and mixers'],
                        ['name' => 'Rice Cooker', 'description' => 'Electric rice cookers'],
                        ['name' => 'Electric Kettle', 'description' => 'Electric kettles'],
                        ['name' => 'Coffee Maker', 'description' => 'Coffee machines'],
                        ['name' => 'Air Fryer', 'description' => 'Air fryers'],
                        ['name' => 'Induction Cooker', 'description' => 'Induction cooktops'],
                    ]],
                    ['name' => 'Personal Care', 'description' => 'Personal care appliances', 'children' => [
                        ['name' => 'Hair Dryer', 'description' => 'Hair dryers'],
                        ['name' => 'Hair Straightener', 'description' => 'Hair straighteners'],
                        ['name' => 'Shaver & Trimmer', 'description' => 'Electric shavers and trimmers'],
                    ]],
                    ['name' => 'Home Care', 'description' => 'Home cleaning appliances', 'children' => [
                        ['name' => 'Vacuum Cleaner', 'description' => 'Vacuum cleaners'],
                        ['name' => 'Robot Vacuum', 'description' => 'Robot vacuum cleaners'],
                        ['name' => 'Iron', 'description' => 'Irons and steamers'],
                        ['name' => 'Washing Machine', 'description' => 'Washing machines'],
                    ]],
                    ['name' => 'Water Purifier', 'description' => 'Water filtration systems'],
                    ['name' => 'Water Heater', 'description' => 'Geysers and water heaters'],
                ],
            ],

            // Server
            [
                'name' => 'Server',
                'description' => 'Servers and server components',
                'icon' => 'server',
                'children' => [
                    ['name' => 'Rack Server', 'description' => 'Rack mount servers'],
                    ['name' => 'Tower Server', 'description' => 'Tower servers'],
                    ['name' => 'Server Component', 'description' => 'Server parts', 'children' => [
                        ['name' => 'Server Processor', 'description' => 'Server CPUs'],
                        ['name' => 'Server RAM', 'description' => 'ECC server memory'],
                        ['name' => 'Server Motherboard', 'description' => 'Server motherboards'],
                        ['name' => 'Server Storage', 'description' => 'Server HDDs and SSDs'],
                    ]],
                    ['name' => 'Server Cabinet', 'description' => 'Server racks and cabinets'],
                ],
            ],
        ];

        $this->createCategories($categories);

        $this->command->info('Categories seeded: ' . Category::count() . ' total');
    }

    private function createCategories(array $categories, ?int $parentId = null, int &$sortOrder = 0): void
    {
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'] ?? null,
                'icon' => $categoryData['icon'] ?? null,
                'parent_id' => $parentId,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'is_featured' => in_array($categoryData['name'], [
                    'Laptop', 'Desktop', 'Component', 'Monitor', 'Gaming', 'Mobile Phone'
                ]),
            ]);

            if (!empty($children)) {
                $this->createCategories($children, $category->id, $sortOrder);
            }
        }
    }
}
