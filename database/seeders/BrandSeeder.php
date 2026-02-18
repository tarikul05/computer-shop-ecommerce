<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            // Processors & Components
            ['name' => 'Intel', 'description' => 'World leader in silicon innovation', 'website' => 'https://www.intel.com', 'featured' => true],
            ['name' => 'AMD', 'description' => 'High-performance computing solutions', 'website' => 'https://www.amd.com', 'featured' => true],
            
            // Graphics Cards
            ['name' => 'NVIDIA', 'description' => 'Visual computing company', 'website' => 'https://www.nvidia.com', 'featured' => true],
            ['name' => 'Gigabyte', 'description' => 'Leading manufacturer of motherboards and graphics cards', 'website' => 'https://www.gigabyte.com', 'featured' => true],
            ['name' => 'MSI', 'description' => 'Gaming and content creation hardware', 'website' => 'https://www.msi.com', 'featured' => true],
            ['name' => 'ASUS', 'description' => 'In search of incredible', 'website' => 'https://www.asus.com', 'featured' => true],
            ['name' => 'EVGA', 'description' => 'Premium gaming hardware', 'website' => 'https://www.evga.com'],
            ['name' => 'Zotac', 'description' => 'Compact and powerful graphics solutions', 'website' => 'https://www.zotac.com'],
            ['name' => 'Colorful', 'description' => 'Gaming graphics cards', 'website' => 'https://www.colorful.cn'],
            
            // Motherboards
            ['name' => 'ASRock', 'description' => 'Challenger in motherboard technology', 'website' => 'https://www.asrock.com'],
            ['name' => 'Biostar', 'description' => 'Reliable motherboard manufacturer', 'website' => 'https://www.biostar.com.tw'],
            
            // Memory
            ['name' => 'Corsair', 'description' => 'High-performance PC components', 'website' => 'https://www.corsair.com', 'featured' => true],
            ['name' => 'G.Skill', 'description' => 'Premium memory solutions', 'website' => 'https://www.gskill.com'],
            ['name' => 'Kingston', 'description' => 'Memory and storage leader', 'website' => 'https://www.kingston.com'],
            ['name' => 'Crucial', 'description' => 'Memory and SSD manufacturer', 'website' => 'https://www.crucial.com'],
            ['name' => 'Team Group', 'description' => 'Memory and storage products', 'website' => 'https://www.teamgroupinc.com'],
            ['name' => 'ADATA', 'description' => 'Memory and storage solutions', 'website' => 'https://www.adata.com'],
            ['name' => 'PNY', 'description' => 'Memory and flash storage', 'website' => 'https://www.pny.com'],
            
            // Storage
            ['name' => 'Samsung', 'description' => 'Technology leader', 'website' => 'https://www.samsung.com', 'featured' => true],
            ['name' => 'Western Digital', 'description' => 'Data storage company', 'website' => 'https://www.westerndigital.com'],
            ['name' => 'Seagate', 'description' => 'Data storage solutions', 'website' => 'https://www.seagate.com'],
            ['name' => 'Toshiba', 'description' => 'Storage and electronics', 'website' => 'https://www.toshiba.com'],
            ['name' => 'SanDisk', 'description' => 'Flash memory products', 'website' => 'https://www.sandisk.com'],
            ['name' => 'Transcend', 'description' => 'Storage and multimedia products', 'website' => 'https://www.transcend-info.com'],
            ['name' => 'Lexar', 'description' => 'Flash memory products', 'website' => 'https://www.lexar.com'],
            
            // Power Supply
            ['name' => 'Seasonic', 'description' => 'Premium power supplies', 'website' => 'https://seasonic.com'],
            ['name' => 'Cooler Master', 'description' => 'Cooling and chassis solutions', 'website' => 'https://www.coolermaster.com'],
            ['name' => 'Thermaltake', 'description' => 'Enthusiast computer hardware', 'website' => 'https://www.thermaltake.com'],
            ['name' => 'be quiet!', 'description' => 'Silent PC components', 'website' => 'https://www.bequiet.com'],
            ['name' => 'NZXT', 'description' => 'PC hardware and software', 'website' => 'https://www.nzxt.com'],
            ['name' => 'Antec', 'description' => 'Power supplies and cases', 'website' => 'https://www.antec.com'],
            ['name' => 'DeepCool', 'description' => 'Cooling solutions', 'website' => 'https://www.deepcool.com'],
            ['name' => 'Lian Li', 'description' => 'Premium PC cases', 'website' => 'https://www.lian-li.com'],
            
            // Monitors
            ['name' => 'LG', 'description' => 'Life is Good', 'website' => 'https://www.lg.com', 'featured' => true],
            ['name' => 'Dell', 'description' => 'Technology solutions company', 'website' => 'https://www.dell.com', 'featured' => true],
            ['name' => 'BenQ', 'description' => 'Display solutions', 'website' => 'https://www.benq.com'],
            ['name' => 'AOC', 'description' => 'Display technology', 'website' => 'https://www.aoc.com'],
            ['name' => 'ViewSonic', 'description' => 'Visual display products', 'website' => 'https://www.viewsonic.com'],
            ['name' => 'Acer', 'description' => 'Computer hardware company', 'website' => 'https://www.acer.com'],
            ['name' => 'Philips', 'description' => 'Health and well-being technology', 'website' => 'https://www.philips.com'],
            
            // Laptops
            ['name' => 'HP', 'description' => 'Hewlett-Packard', 'website' => 'https://www.hp.com', 'featured' => true],
            ['name' => 'Lenovo', 'description' => 'Smarter technology for all', 'website' => 'https://www.lenovo.com', 'featured' => true],
            ['name' => 'Apple', 'description' => 'Think Different', 'website' => 'https://www.apple.com', 'featured' => true],
            ['name' => 'Microsoft', 'description' => 'Surface and software', 'website' => 'https://www.microsoft.com'],
            ['name' => 'Razer', 'description' => 'For Gamers. By Gamers.', 'website' => 'https://www.razer.com'],
            ['name' => 'Alienware', 'description' => 'High-end gaming', 'website' => 'https://www.alienware.com'],
            
            // Peripherals
            ['name' => 'Logitech', 'description' => 'Personal peripherals', 'website' => 'https://www.logitech.com', 'featured' => true],
            ['name' => 'SteelSeries', 'description' => 'Gaming peripherals', 'website' => 'https://steelseries.com'],
            ['name' => 'HyperX', 'description' => 'Gaming gear', 'website' => 'https://www.hyperxgaming.com'],
            ['name' => 'Redragon', 'description' => 'Gaming peripherals', 'website' => 'https://www.redragonzone.com'],
            ['name' => 'A4Tech', 'description' => 'Computer peripherals', 'website' => 'https://www.a4tech.com'],
            ['name' => 'Rapoo', 'description' => 'Wireless peripherals', 'website' => 'https://www.rapoo.com'],
            ['name' => 'Fantech', 'description' => 'Gaming gear', 'website' => 'https://www.fantechworld.com'],
            ['name' => 'Havit', 'description' => 'Digital peripherals', 'website' => 'https://www.havit.hk'],
            ['name' => 'Glorious', 'description' => 'PC Gaming Race', 'website' => 'https://www.gloriousgaming.com'],
            ['name' => 'Keychron', 'description' => 'Wireless mechanical keyboards', 'website' => 'https://www.keychron.com'],
            
            // Audio
            ['name' => 'JBL', 'description' => 'Audio equipment', 'website' => 'https://www.jbl.com'],
            ['name' => 'Audio-Technica', 'description' => 'Professional audio equipment', 'website' => 'https://www.audio-technica.com'],
            ['name' => 'Sennheiser', 'description' => 'Audio specialist', 'website' => 'https://www.sennheiser.com'],
            ['name' => 'Sony', 'description' => 'Electronics and entertainment', 'website' => 'https://www.sony.com'],
            ['name' => 'Edifier', 'description' => 'Speaker systems', 'website' => 'https://www.edifier.com'],
            ['name' => 'Creative', 'description' => 'Digital entertainment products', 'website' => 'https://www.creative.com'],
            
            // Networking
            ['name' => 'TP-Link', 'description' => 'Networking products', 'website' => 'https://www.tp-link.com', 'featured' => true],
            ['name' => 'D-Link', 'description' => 'Network solutions', 'website' => 'https://www.dlink.com'],
            ['name' => 'Netgear', 'description' => 'Home networking', 'website' => 'https://www.netgear.com'],
            ['name' => 'Tenda', 'description' => 'Networking devices', 'website' => 'https://www.tendacn.com'],
            ['name' => 'Cisco', 'description' => 'Enterprise networking', 'website' => 'https://www.cisco.com'],
            ['name' => 'Ubiquiti', 'description' => 'Enterprise wireless', 'website' => 'https://www.ui.com'],
            ['name' => 'MikroTik', 'description' => 'Network equipment', 'website' => 'https://mikrotik.com'],
            
            // UPS
            ['name' => 'APC', 'description' => 'Power protection', 'website' => 'https://www.apc.com'],
            ['name' => 'CyberPower', 'description' => 'Power solutions', 'website' => 'https://www.cyberpowersystems.com'],
            ['name' => 'Vertiv', 'description' => 'Critical digital infrastructure', 'website' => 'https://www.vertiv.com'],
            ['name' => 'Power Guard', 'description' => 'UPS systems', 'website' => null],
            
            // Printers
            ['name' => 'Canon', 'description' => 'Imaging and optical products', 'website' => 'https://www.canon.com'],
            ['name' => 'Epson', 'description' => 'Printers and projectors', 'website' => 'https://www.epson.com'],
            ['name' => 'Brother', 'description' => 'Printing solutions', 'website' => 'https://www.brother.com'],
            
            // Gaming Furniture
            ['name' => 'Secretlab', 'description' => 'Gaming chairs', 'website' => 'https://secretlab.co'],
            ['name' => 'DXRacer', 'description' => 'Gaming chairs', 'website' => 'https://www.dxracer.com'],
            ['name' => 'Cougar', 'description' => 'Gaming hardware', 'website' => 'https://www.cougargaming.com'],
            
            // Gaming Consoles
            ['name' => 'PlayStation', 'description' => 'Sony gaming', 'website' => 'https://www.playstation.com'],
            ['name' => 'Xbox', 'description' => 'Microsoft gaming', 'website' => 'https://www.xbox.com'],
            ['name' => 'Nintendo', 'description' => 'Video game company', 'website' => 'https://www.nintendo.com'],
        ];

        $sortOrder = 0;
        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'description' => $brand['description'],
                'website' => $brand['website'] ?? null,
                'is_active' => true,
                'is_featured' => $brand['featured'] ?? false,
                'sort_order' => $sortOrder++,
            ]);
        }

        $this->command->info('Brands seeded: ' . Brand::count() . ' total');
    }
}
