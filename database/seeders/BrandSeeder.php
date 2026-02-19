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
            // Laptop Brands (from Ryans)
            ['name' => 'Acer', 'description' => 'Computer hardware company', 'website' => 'https://www.acer.com', 'featured' => true],
            ['name' => 'Apple', 'description' => 'Think Different', 'website' => 'https://www.apple.com', 'featured' => true],
            ['name' => 'ASUS', 'description' => 'In search of incredible', 'website' => 'https://www.asus.com', 'featured' => true],
            ['name' => 'CHUWI', 'description' => 'Innovative computing devices', 'website' => 'https://www.chuwi.com'],
            ['name' => 'Dell', 'description' => 'Technology solutions company', 'website' => 'https://www.dell.com', 'featured' => true],
            ['name' => 'Gigabyte', 'description' => 'Leading manufacturer of motherboards and graphics cards', 'website' => 'https://www.gigabyte.com', 'featured' => true],
            ['name' => 'HP', 'description' => 'Hewlett-Packard', 'website' => 'https://www.hp.com', 'featured' => true],
            ['name' => 'Lenovo', 'description' => 'Smarter technology for all', 'website' => 'https://www.lenovo.com', 'featured' => true],
            ['name' => 'Microsoft', 'description' => 'Surface and software', 'website' => 'https://www.microsoft.com', 'featured' => true],
            ['name' => 'MSI', 'description' => 'Gaming and content creation hardware', 'website' => 'https://www.msi.com', 'featured' => true],
            ['name' => 'Smart', 'description' => 'Smart computing solutions'],
            ['name' => 'TECNO', 'description' => 'Mobile and computing devices', 'website' => 'https://www.tecno-mobile.com'],

            // Processors & Components
            ['name' => 'Intel', 'description' => 'World leader in silicon innovation', 'website' => 'https://www.intel.com', 'featured' => true],
            ['name' => 'AMD', 'description' => 'High-performance computing solutions', 'website' => 'https://www.amd.com', 'featured' => true],
            
            // Graphics Cards
            ['name' => 'NVIDIA', 'description' => 'Visual computing company', 'website' => 'https://www.nvidia.com', 'featured' => true],
            ['name' => 'EVGA', 'description' => 'Premium gaming hardware', 'website' => 'https://www.evga.com'],
            ['name' => 'Zotac', 'description' => 'Compact and powerful graphics solutions', 'website' => 'https://www.zotac.com'],
            ['name' => 'Colorful', 'description' => 'Gaming graphics cards', 'website' => 'https://www.colorful.cn'],
            ['name' => 'Inno3D', 'description' => 'Gaming graphics cards', 'website' => 'https://www.inno3d.com'],
            ['name' => 'Palit', 'description' => 'Graphics card manufacturer', 'website' => 'https://www.palit.com'],
            ['name' => 'Sapphire', 'description' => 'AMD graphics card specialist', 'website' => 'https://www.sapphiretech.com'],
            ['name' => 'PowerColor', 'description' => 'AMD graphics cards', 'website' => 'https://www.powercolor.com'],
            ['name' => 'XFX', 'description' => 'Graphics cards and power supplies', 'website' => 'https://www.xfxforce.com'],
            
            // Motherboards
            ['name' => 'ASRock', 'description' => 'Challenger in motherboard technology', 'website' => 'https://www.asrock.com'],
            ['name' => 'Biostar', 'description' => 'Reliable motherboard manufacturer', 'website' => 'https://www.biostar.com.tw'],
            
            // Memory
            ['name' => 'Corsair', 'description' => 'High-performance PC components', 'website' => 'https://www.corsair.com', 'featured' => true],
            ['name' => 'G.Skill', 'description' => 'Premium memory solutions', 'website' => 'https://www.gskill.com'],
            ['name' => 'Kingston', 'description' => 'Memory and storage leader', 'website' => 'https://www.kingston.com', 'featured' => true],
            ['name' => 'Crucial', 'description' => 'Memory and SSD manufacturer', 'website' => 'https://www.crucial.com'],
            ['name' => 'Team Group', 'description' => 'Memory and storage products', 'website' => 'https://www.teamgroupinc.com'],
            ['name' => 'ADATA', 'description' => 'Memory and storage solutions', 'website' => 'https://www.adata.com'],
            ['name' => 'PNY', 'description' => 'Memory and flash storage', 'website' => 'https://www.pny.com'],
            ['name' => 'Patriot', 'description' => 'Memory and storage', 'website' => 'https://www.patriotmemory.com'],
            ['name' => 'Apacer', 'description' => 'Digital storage solutions', 'website' => 'https://www.apacer.com'],
            
            // Storage
            ['name' => 'Samsung', 'description' => 'Technology leader', 'website' => 'https://www.samsung.com', 'featured' => true],
            ['name' => 'Western Digital', 'description' => 'Data storage company', 'website' => 'https://www.westerndigital.com', 'featured' => true],
            ['name' => 'WD', 'description' => 'Western Digital consumer brand', 'website' => 'https://www.westerndigital.com'],
            ['name' => 'Seagate', 'description' => 'Data storage solutions', 'website' => 'https://www.seagate.com', 'featured' => true],
            ['name' => 'Toshiba', 'description' => 'Storage and electronics', 'website' => 'https://www.toshiba.com'],
            ['name' => 'SanDisk', 'description' => 'Flash memory products', 'website' => 'https://www.sandisk.com'],
            ['name' => 'Transcend', 'description' => 'Storage and multimedia products', 'website' => 'https://www.transcend-info.com'],
            ['name' => 'Lexar', 'description' => 'Flash memory products', 'website' => 'https://www.lexar.com'],
            ['name' => 'Hikvision', 'description' => 'Security and storage solutions', 'website' => 'https://www.hikvision.com'],
            ['name' => 'Netac', 'description' => 'Flash memory products', 'website' => 'https://www.netac.com'],
            ['name' => 'Addlink', 'description' => 'Storage solutions', 'website' => 'https://www.addlink.com.tw'],
            ['name' => 'Silicon Power', 'description' => 'Memory and storage', 'website' => 'https://www.silicon-power.com'],
            
            // Power Supply
            ['name' => 'Seasonic', 'description' => 'Premium power supplies', 'website' => 'https://seasonic.com'],
            ['name' => 'Cooler Master', 'description' => 'Cooling and chassis solutions', 'website' => 'https://www.coolermaster.com', 'featured' => true],
            ['name' => 'Thermaltake', 'description' => 'Enthusiast computer hardware', 'website' => 'https://www.thermaltake.com'],
            ['name' => 'be quiet!', 'description' => 'Silent PC components', 'website' => 'https://www.bequiet.com'],
            ['name' => 'NZXT', 'description' => 'PC hardware and software', 'website' => 'https://www.nzxt.com'],
            ['name' => 'Antec', 'description' => 'Power supplies and cases', 'website' => 'https://www.antec.com'],
            ['name' => 'DeepCool', 'description' => 'Cooling solutions', 'website' => 'https://www.deepcool.com'],
            ['name' => 'Lian Li', 'description' => 'Premium PC cases', 'website' => 'https://www.lian-li.com'],
            ['name' => 'FSP', 'description' => 'Power supply manufacturer', 'website' => 'https://www.fsplifestyle.com'],
            ['name' => 'Super Flower', 'description' => 'Power supply units', 'website' => 'https://www.super-flower.com.tw'],
            ['name' => 'Value Top', 'description' => 'Budget PC components'],
            ['name' => 'PC Power', 'description' => 'Budget PC components'],
            ['name' => 'Gamdias', 'description' => 'Gaming peripherals and components', 'website' => 'https://www.gamdias.com'],
            ['name' => 'Montech', 'description' => 'PC cases and PSU', 'website' => 'https://www.montech.tw'],
            
            // Monitors (from Ryans)
            ['name' => 'LG', 'description' => 'Life is Good', 'website' => 'https://www.lg.com', 'featured' => true],
            ['name' => 'BenQ', 'description' => 'Display solutions', 'website' => 'https://www.benq.com', 'featured' => true],
            ['name' => 'AOC', 'description' => 'Display technology', 'website' => 'https://www.aoc.com'],
            ['name' => 'ViewSonic', 'description' => 'Visual display products', 'website' => 'https://www.viewsonic.com'],
            ['name' => 'Philips', 'description' => 'Health and well-being technology', 'website' => 'https://www.philips.com'],
            ['name' => 'Dahua', 'description' => 'Security and display solutions', 'website' => 'https://www.dahuasecurity.com'],
            ['name' => 'Arzopa', 'description' => 'Portable monitors', 'website' => 'https://www.arzopa.com'],
            ['name' => 'Xiaomi', 'description' => 'Consumer electronics', 'website' => 'https://www.mi.com', 'featured' => true],
            
            // Tablets (from Ryans)
            ['name' => 'Amazon', 'description' => 'Fire tablets and devices', 'website' => 'https://www.amazon.com'],
            ['name' => 'Google', 'description' => 'Pixel devices', 'website' => 'https://www.google.com'],
            ['name' => 'HONOR', 'description' => 'Smart devices', 'website' => 'https://www.hihonor.com'],
            ['name' => 'OnePlus', 'description' => 'Never Settle', 'website' => 'https://www.oneplus.com'],
            ['name' => 'Teclast', 'description' => 'Tablet manufacturer', 'website' => 'https://www.teclast.com'],
            ['name' => 'Huion', 'description' => 'Graphics tablets', 'website' => 'https://www.huion.com'],
            ['name' => 'Wacom', 'description' => 'Professional graphics tablets', 'website' => 'https://www.wacom.com'],
            ['name' => 'XP-Pen', 'description' => 'Graphics tablets', 'website' => 'https://www.xp-pen.com'],
            
            // Peripherals
            ['name' => 'Logitech', 'description' => 'Personal peripherals', 'website' => 'https://www.logitech.com', 'featured' => true],
            ['name' => 'Razer', 'description' => 'For Gamers. By Gamers.', 'website' => 'https://www.razer.com', 'featured' => true],
            ['name' => 'SteelSeries', 'description' => 'Gaming peripherals', 'website' => 'https://steelseries.com'],
            ['name' => 'HyperX', 'description' => 'Gaming gear', 'website' => 'https://www.hyperxgaming.com'],
            ['name' => 'Redragon', 'description' => 'Gaming peripherals', 'website' => 'https://www.redragonzone.com'],
            ['name' => 'A4Tech', 'description' => 'Computer peripherals', 'website' => 'https://www.a4tech.com'],
            ['name' => 'Rapoo', 'description' => 'Wireless peripherals', 'website' => 'https://www.rapoo.com'],
            ['name' => 'Fantech', 'description' => 'Gaming gear', 'website' => 'https://www.fantechworld.com'],
            ['name' => 'Havit', 'description' => 'Digital peripherals', 'website' => 'https://www.havit.hk'],
            ['name' => 'Glorious', 'description' => 'PC Gaming Race', 'website' => 'https://www.gloriousgaming.com'],
            ['name' => 'Keychron', 'description' => 'Wireless mechanical keyboards', 'website' => 'https://www.keychron.com'],
            ['name' => 'Ducky', 'description' => 'Mechanical keyboards', 'website' => 'https://www.duckychannel.com.tw'],
            ['name' => 'Royal Kludge', 'description' => 'Mechanical keyboards', 'website' => 'https://www.rkgaming.com'],
            ['name' => 'Akko', 'description' => 'Mechanical keyboards', 'website' => 'https://www.akkogear.com'],
            ['name' => 'Dareu', 'description' => 'Gaming peripherals', 'website' => 'https://www.dareu.com'],
            ['name' => 'Micropack', 'description' => 'Computer peripherals', 'website' => 'https://www.micropack.co'],
            ['name' => 'Delux', 'description' => 'Computer peripherals', 'website' => 'https://www.deluxworld.com'],
            ['name' => 'UGREEN', 'description' => 'Computer accessories', 'website' => 'https://www.ugreen.com'],
            ['name' => 'Baseus', 'description' => 'Digital accessories', 'website' => 'https://www.baseus.com'],
            ['name' => 'Orico', 'description' => 'Digital accessories', 'website' => 'https://www.orico.cc'],
            ['name' => 'Vention', 'description' => 'Cables and adapters', 'website' => 'https://www.vention.com'],
            
            // Audio
            ['name' => 'JBL', 'description' => 'Audio equipment', 'website' => 'https://www.jbl.com', 'featured' => true],
            ['name' => 'Audio-Technica', 'description' => 'Professional audio equipment', 'website' => 'https://www.audio-technica.com'],
            ['name' => 'Sennheiser', 'description' => 'Audio specialist', 'website' => 'https://www.sennheiser.com'],
            ['name' => 'Sony', 'description' => 'Electronics and entertainment', 'website' => 'https://www.sony.com', 'featured' => true],
            ['name' => 'Edifier', 'description' => 'Speaker systems', 'website' => 'https://www.edifier.com'],
            ['name' => 'Creative', 'description' => 'Digital entertainment products', 'website' => 'https://www.creative.com'],
            ['name' => 'Bose', 'description' => 'Premium audio', 'website' => 'https://www.bose.com'],
            ['name' => 'Marshall', 'description' => 'Iconic audio', 'website' => 'https://www.marshallheadphones.com'],
            ['name' => 'Bang & Olufsen', 'description' => 'Premium audio', 'website' => 'https://www.bang-olufsen.com'],
            ['name' => 'Harman Kardon', 'description' => 'Premium audio', 'website' => 'https://www.harmankardon.com'],
            ['name' => 'Soundcore', 'description' => 'Audio by Anker', 'website' => 'https://www.soundcore.com'],
            ['name' => 'QCY', 'description' => 'Wireless earphones', 'website' => 'https://www.qcyear.com'],
            ['name' => 'Haylou', 'description' => 'Smart audio devices', 'website' => 'https://www.haylou.com'],
            ['name' => 'Rode', 'description' => 'Professional microphones', 'website' => 'https://www.rode.com'],
            ['name' => 'Blue', 'description' => 'Microphones', 'website' => 'https://www.bluedesigns.com'],
            ['name' => 'Shure', 'description' => 'Professional audio', 'website' => 'https://www.shure.com'],
            ['name' => 'Maono', 'description' => 'Audio equipment', 'website' => 'https://www.maono.com'],
            ['name' => 'Fifine', 'description' => 'Microphones', 'website' => 'https://www.fifinemicrophone.com'],
            ['name' => 'Boya', 'description' => 'Audio equipment', 'website' => 'https://www.boya-mic.com'],
            
            // Networking
            ['name' => 'TP-Link', 'description' => 'Networking products', 'website' => 'https://www.tp-link.com', 'featured' => true],
            ['name' => 'D-Link', 'description' => 'Network solutions', 'website' => 'https://www.dlink.com'],
            ['name' => 'Netgear', 'description' => 'Home networking', 'website' => 'https://www.netgear.com'],
            ['name' => 'Tenda', 'description' => 'Networking devices', 'website' => 'https://www.tendacn.com'],
            ['name' => 'Cisco', 'description' => 'Enterprise networking', 'website' => 'https://www.cisco.com'],
            ['name' => 'Ubiquiti', 'description' => 'Enterprise wireless', 'website' => 'https://www.ui.com'],
            ['name' => 'MikroTik', 'description' => 'Network equipment', 'website' => 'https://mikrotik.com'],
            ['name' => 'Ruijie', 'description' => 'Network solutions', 'website' => 'https://www.ruijienetworks.com'],
            ['name' => 'Cudy', 'description' => 'Networking products', 'website' => 'https://www.cudy.com'],
            ['name' => 'Mercusys', 'description' => 'Networking products', 'website' => 'https://www.mercusys.com'],
            ['name' => 'Starlink', 'description' => 'Satellite internet', 'website' => 'https://www.starlink.com'],
            
            // UPS
            ['name' => 'APC', 'description' => 'Power protection', 'website' => 'https://www.apc.com', 'featured' => true],
            ['name' => 'CyberPower', 'description' => 'Power solutions', 'website' => 'https://www.cyberpowersystems.com'],
            ['name' => 'Vertiv', 'description' => 'Critical digital infrastructure', 'website' => 'https://www.vertiv.com'],
            ['name' => 'Power Guard', 'description' => 'UPS systems'],
            ['name' => 'Prolink', 'description' => 'Power solutions', 'website' => 'https://www.prolink.com.my'],
            ['name' => 'Luminous', 'description' => 'Power solutions', 'website' => 'https://www.luminousindia.com'],
            ['name' => 'Power Tree', 'description' => 'UPS systems'],
            ['name' => 'MaxGreen', 'description' => 'Power solutions'],
            
            // Printers
            ['name' => 'Canon', 'description' => 'Imaging and optical products', 'website' => 'https://www.canon.com', 'featured' => true],
            ['name' => 'Epson', 'description' => 'Printers and projectors', 'website' => 'https://www.epson.com', 'featured' => true],
            ['name' => 'Brother', 'description' => 'Printing solutions', 'website' => 'https://www.brother.com'],
            ['name' => 'Ricoh', 'description' => 'Imaging and electronics', 'website' => 'https://www.ricoh.com'],
            ['name' => 'Pantum', 'description' => 'Printing solutions', 'website' => 'https://www.pantum.com'],
            ['name' => 'Kyocera', 'description' => 'Business printers', 'website' => 'https://www.kyoceradocumentsolutions.com'],
            ['name' => 'Zebra', 'description' => 'Label and barcode printers', 'website' => 'https://www.zebra.com'],
            ['name' => 'TSC', 'description' => 'Barcode printers', 'website' => 'https://www.tscprinters.com'],
            ['name' => 'Godex', 'description' => 'Barcode printers', 'website' => 'https://www.godexprinters.com'],
            ['name' => 'Honeywell', 'description' => 'Industrial solutions', 'website' => 'https://www.honeywell.com'],
            
            // Camera
            ['name' => 'Nikon', 'description' => 'Optical and imaging products', 'website' => 'https://www.nikon.com', 'featured' => true],
            ['name' => 'Fujifilm', 'description' => 'Imaging products', 'website' => 'https://www.fujifilm.com'],
            ['name' => 'Panasonic', 'description' => 'Electronics', 'website' => 'https://www.panasonic.com'],
            ['name' => 'GoPro', 'description' => 'Action cameras', 'website' => 'https://www.gopro.com'],
            ['name' => 'DJI', 'description' => 'Drones and cameras', 'website' => 'https://www.dji.com', 'featured' => true],
            ['name' => 'Insta360', 'description' => '360 cameras', 'website' => 'https://www.insta360.com'],
            
            // Security
            ['name' => 'Imou', 'description' => 'Smart security', 'website' => 'https://www.imoulife.com'],
            ['name' => 'Ezviz', 'description' => 'Smart home security', 'website' => 'https://www.ezviz.com'],
            ['name' => 'TP-Link Tapo', 'description' => 'Smart home devices', 'website' => 'https://www.tapo.com'],
            ['name' => 'ZKTeco', 'description' => 'Security and time attendance', 'website' => 'https://www.zkteco.com'],
            ['name' => 'Yale', 'description' => 'Smart locks', 'website' => 'https://www.yalehome.com'],
            ['name' => 'Ring', 'description' => 'Smart doorbells', 'website' => 'https://www.ring.com'],
            
            // Gaming Furniture & Accessories
            ['name' => 'Secretlab', 'description' => 'Gaming chairs', 'website' => 'https://secretlab.co'],
            ['name' => 'DXRacer', 'description' => 'Gaming chairs', 'website' => 'https://www.dxracer.com'],
            ['name' => 'Cougar', 'description' => 'Gaming hardware', 'website' => 'https://www.cougargaming.com'],
            ['name' => 'AKRacing', 'description' => 'Gaming chairs', 'website' => 'https://www.akracing.com'],
            ['name' => 'noblechairs', 'description' => 'Premium gaming chairs', 'website' => 'https://www.noblechairs.com'],
            ['name' => 'GT Racing', 'description' => 'Gaming chairs', 'website' => 'https://www.gtracing.com'],
            ['name' => 'RESPAWN', 'description' => 'Gaming furniture', 'website' => 'https://www.respawnproducts.com'],
            
            // Gaming Consoles
            ['name' => 'PlayStation', 'description' => 'Sony gaming', 'website' => 'https://www.playstation.com', 'featured' => true],
            ['name' => 'Xbox', 'description' => 'Microsoft gaming', 'website' => 'https://www.xbox.com', 'featured' => true],
            ['name' => 'Nintendo', 'description' => 'Video game company', 'website' => 'https://www.nintendo.com', 'featured' => true],
            ['name' => 'Steam Deck', 'description' => 'Portable gaming PC', 'website' => 'https://www.steamdeck.com'],
            ['name' => 'Meta', 'description' => 'VR headsets', 'website' => 'https://www.meta.com'],
            
            // Projectors
            ['name' => 'Optoma', 'description' => 'Projector solutions', 'website' => 'https://www.optoma.com'],
            ['name' => 'InFocus', 'description' => 'Display solutions', 'website' => 'https://www.infocus.com'],
            ['name' => 'Vivitek', 'description' => 'Visual display solutions', 'website' => 'https://www.vivitek.com'],
            ['name' => 'NEC', 'description' => 'Display solutions', 'website' => 'https://www.nec-display.com'],
            ['name' => 'Casio', 'description' => 'Electronics', 'website' => 'https://www.casio.com'],
            
            // Mobile Phones
            ['name' => 'Realme', 'description' => 'Dare to Leap', 'website' => 'https://www.realme.com'],
            ['name' => 'OPPO', 'description' => 'Inspiration Ahead', 'website' => 'https://www.oppo.com'],
            ['name' => 'Vivo', 'description' => 'Camera & Music', 'website' => 'https://www.vivo.com'],
            ['name' => 'Nothing', 'description' => 'Tech startup', 'website' => 'https://www.nothing.tech'],
            ['name' => 'Infinix', 'description' => 'The Future is Now', 'website' => 'https://www.infinixmobility.com'],
            ['name' => 'ITEL', 'description' => 'Enjoy Better Life', 'website' => 'https://www.itel-mobile.com'],
            ['name' => 'Walton', 'description' => 'Bangladesh electronics', 'website' => 'https://www.waltonbd.com'],
            ['name' => 'Symphony', 'description' => 'Mobile phones', 'website' => 'https://www.symphony-mobile.com'],
            
            // Smartwatches & Wearables
            ['name' => 'Amazfit', 'description' => 'Smart wearables', 'website' => 'https://www.amazfit.com'],
            ['name' => 'Garmin', 'description' => 'GPS and wearables', 'website' => 'https://www.garmin.com'],
            ['name' => 'Fitbit', 'description' => 'Fitness trackers', 'website' => 'https://www.fitbit.com'],
            ['name' => 'Huawei', 'description' => 'Smart devices', 'website' => 'https://www.huawei.com'],
            
            // Appliances
            ['name' => 'Haier', 'description' => 'Home appliances', 'website' => 'https://www.haier.com'],
            ['name' => 'Midea', 'description' => 'Home appliances', 'website' => 'https://www.midea.com'],
            ['name' => 'Gree', 'description' => 'Air conditioning', 'website' => 'https://www.gree.com'],
            ['name' => 'Daikin', 'description' => 'Air conditioning', 'website' => 'https://www.daikin.com'],
            ['name' => 'Sharp', 'description' => 'Electronics', 'website' => 'https://www.sharp.com'],
            ['name' => 'Philips Domestic', 'description' => 'Home appliances', 'website' => 'https://www.philips.com'],
            ['name' => 'Electrolux', 'description' => 'Home appliances', 'website' => 'https://www.electrolux.com'],
            ['name' => 'Whirlpool', 'description' => 'Home appliances', 'website' => 'https://www.whirlpool.com'],
            ['name' => 'Dyson', 'description' => 'Home appliances', 'website' => 'https://www.dyson.com'],
            ['name' => 'iRobot', 'description' => 'Robot vacuums', 'website' => 'https://www.irobot.com'],
            ['name' => 'Roborock', 'description' => 'Robot vacuums', 'website' => 'https://www.roborock.com'],
            ['name' => 'Ecovacs', 'description' => 'Robot vacuums', 'website' => 'https://www.ecovacs.com'],
            ['name' => 'Dreame', 'description' => 'Home cleaning', 'website' => 'https://www.dreame.com'],
            
            // Office Equipment
            ['name' => 'Konica Minolta', 'description' => 'Office equipment', 'website' => 'https://www.konicaminolta.com'],
            ['name' => 'Sharp Business', 'description' => 'Business equipment', 'website' => 'https://www.sharp.com'],
            ['name' => 'Fujitsu', 'description' => 'IT equipment', 'website' => 'https://www.fujitsu.com'],
            ['name' => 'Avision', 'description' => 'Scanner solutions', 'website' => 'https://www.avision.com'],
            ['name' => 'Plustek', 'description' => 'Scanners', 'website' => 'https://www.plustek.com'],
            ['name' => 'Logitech Business', 'description' => 'Video conferencing', 'website' => 'https://www.logitech.com'],
            ['name' => 'Poly', 'description' => 'Video conferencing', 'website' => 'https://www.poly.com'],
            ['name' => 'Jabra', 'description' => 'Audio and video', 'website' => 'https://www.jabra.com'],
            ['name' => 'Yealink', 'description' => 'Video conferencing', 'website' => 'https://www.yealink.com'],
            ['name' => 'Grandstream', 'description' => 'IP phones', 'website' => 'https://www.grandstream.com'],
            ['name' => 'Fanvil', 'description' => 'IP phones', 'website' => 'https://www.fanvil.com'],
            
            // Software
            ['name' => 'Kaspersky', 'description' => 'Security software', 'website' => 'https://www.kaspersky.com'],
            ['name' => 'Bitdefender', 'description' => 'Security software', 'website' => 'https://www.bitdefender.com'],
            ['name' => 'ESET', 'description' => 'Security software', 'website' => 'https://www.eset.com'],
            ['name' => 'Norton', 'description' => 'Security software', 'website' => 'https://www.norton.com'],
            ['name' => 'McAfee', 'description' => 'Security software', 'website' => 'https://www.mcafee.com'],
            ['name' => 'Trend Micro', 'description' => 'Security software', 'website' => 'https://www.trendmicro.com'],
            ['name' => 'Adobe', 'description' => 'Creative software', 'website' => 'https://www.adobe.com'],
            ['name' => 'Autodesk', 'description' => 'Engineering software', 'website' => 'https://www.autodesk.com'],
            ['name' => 'Corel', 'description' => 'Creative software', 'website' => 'https://www.corel.com'],
            
            // Server
            ['name' => 'Supermicro', 'description' => 'Server solutions', 'website' => 'https://www.supermicro.com'],
            ['name' => 'HPE', 'description' => 'Hewlett Packard Enterprise', 'website' => 'https://www.hpe.com'],
            ['name' => 'Dell EMC', 'description' => 'Enterprise solutions', 'website' => 'https://www.delltechnologies.com'],
            ['name' => 'IBM', 'description' => 'Enterprise computing', 'website' => 'https://www.ibm.com'],
            ['name' => 'Synology', 'description' => 'NAS solutions', 'website' => 'https://www.synology.com'],
            ['name' => 'QNAP', 'description' => 'NAS solutions', 'website' => 'https://www.qnap.com'],
            ['name' => 'Terramaster', 'description' => 'NAS solutions', 'website' => 'https://www.terra-master.com'],
            
            // Others
            ['name' => 'Anker', 'description' => 'Charging technology', 'website' => 'https://www.anker.com', 'featured' => true],
            ['name' => 'Belkin', 'description' => 'Computer accessories', 'website' => 'https://www.belkin.com'],
            ['name' => 'Targus', 'description' => 'Laptop bags and accessories', 'website' => 'https://www.targus.com'],
            ['name' => 'Samsonite', 'description' => 'Bags and luggage', 'website' => 'https://www.samsonite.com'],
            ['name' => 'Energizer', 'description' => 'Batteries and power', 'website' => 'https://www.energizer.com'],
            ['name' => 'Duracell', 'description' => 'Batteries', 'website' => 'https://www.duracell.com'],
            ['name' => 'Arctic', 'description' => 'Cooling solutions', 'website' => 'https://www.arctic.de'],
            ['name' => 'Noctua', 'description' => 'Premium cooling', 'website' => 'https://www.noctua.at'],
            ['name' => 'Thermalright', 'description' => 'Cooling solutions', 'website' => 'https://www.thermalright.com'],
            ['name' => 'ID-Cooling', 'description' => 'Cooling solutions', 'website' => 'https://www.idcooling.com'],
            ['name' => 'EKWB', 'description' => 'Water cooling', 'website' => 'https://www.ekwb.com'],
            ['name' => 'Alphacool', 'description' => 'Water cooling', 'website' => 'https://www.alphacool.com'],
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
