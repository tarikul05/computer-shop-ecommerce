<?php

namespace Database\Seeders;

use App\Modules\Search\Models\SearchSynonym;
use App\Modules\Search\Models\PopularSearch;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create search synonyms for common computer/tech terms
        $synonyms = [
            [
                'term' => 'laptop',
                'synonyms' => ['notebook', 'portable computer', 'macbook', 'ultrabook'],
            ],
            [
                'term' => 'desktop',
                'synonyms' => ['pc', 'computer', 'workstation', 'tower'],
            ],
            [
                'term' => 'monitor',
                'synonyms' => ['display', 'screen', 'led monitor', 'lcd'],
            ],
            [
                'term' => 'keyboard',
                'synonyms' => ['kb', 'mechanical keyboard', 'gaming keyboard'],
            ],
            [
                'term' => 'mouse',
                'synonyms' => ['mice', 'gaming mouse', 'wireless mouse', 'optical mouse'],
            ],
            [
                'term' => 'ram',
                'synonyms' => ['memory', 'ddr4', 'ddr5', 'system memory'],
            ],
            [
                'term' => 'ssd',
                'synonyms' => ['solid state drive', 'nvme', 'm.2', 'storage'],
            ],
            [
                'term' => 'hdd',
                'synonyms' => ['hard drive', 'hard disk', 'storage'],
            ],
            [
                'term' => 'gpu',
                'synonyms' => ['graphics card', 'video card', 'vga', 'rtx', 'gtx'],
            ],
            [
                'term' => 'cpu',
                'synonyms' => ['processor', 'intel', 'amd', 'ryzen', 'core i7', 'core i5'],
            ],
            [
                'term' => 'motherboard',
                'synonyms' => ['mainboard', 'mobo', 'system board'],
            ],
            [
                'term' => 'psu',
                'synonyms' => ['power supply', 'power unit', 'smps'],
            ],
            [
                'term' => 'case',
                'synonyms' => ['cabinet', 'chassis', 'tower case', 'casing'],
            ],
            [
                'term' => 'headphone',
                'synonyms' => ['headset', 'earphone', 'gaming headset', 'earbuds'],
            ],
            [
                'term' => 'speaker',
                'synonyms' => ['sound system', 'audio', 'subwoofer', 'soundbar'],
            ],
            [
                'term' => 'router',
                'synonyms' => ['wifi router', 'wireless router', 'network router'],
            ],
            [
                'term' => 'ups',
                'synonyms' => ['uninterruptible power supply', 'battery backup', 'ips'],
            ],
            [
                'term' => 'webcam',
                'synonyms' => ['web camera', 'camera', 'streaming camera'],
            ],
            [
                'term' => 'printer',
                'synonyms' => ['inkjet', 'laser printer', 'all in one'],
            ],
            [
                'term' => 'gaming',
                'synonyms' => ['gamer', 'esports', 'game'],
            ],
        ];

        foreach ($synonyms as $synonymData) {
            SearchSynonym::updateOrCreate(
                ['term' => $synonymData['term']],
                [
                    'synonyms' => $synonymData['synonyms'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Created ' . count($synonyms) . ' search synonyms');

        // Create some initial popular searches
        $popularSearches = [
            ['query' => 'gaming laptop', 'search_count' => 156, 'click_count' => 89, 'conversion_count' => 12],
            ['query' => 'rtx 4090', 'search_count' => 142, 'click_count' => 78, 'conversion_count' => 8],
            ['query' => 'mechanical keyboard', 'search_count' => 128, 'click_count' => 65, 'conversion_count' => 15],
            ['query' => 'intel core i7', 'search_count' => 115, 'click_count' => 52, 'conversion_count' => 6],
            ['query' => 'samsung ssd', 'search_count' => 98, 'click_count' => 45, 'conversion_count' => 10],
            ['query' => '27 inch monitor', 'search_count' => 87, 'click_count' => 41, 'conversion_count' => 7],
            ['query' => 'gaming mouse', 'search_count' => 82, 'click_count' => 38, 'conversion_count' => 9],
            ['query' => 'amd ryzen', 'search_count' => 76, 'click_count' => 35, 'conversion_count' => 5],
            ['query' => 'corsair ram', 'search_count' => 68, 'click_count' => 32, 'conversion_count' => 8],
            ['query' => 'wireless headset', 'search_count' => 62, 'click_count' => 28, 'conversion_count' => 6],
            ['query' => 'hp laptop', 'search_count' => 58, 'click_count' => 25, 'conversion_count' => 4],
            ['query' => 'dell monitor', 'search_count' => 54, 'click_count' => 22, 'conversion_count' => 5],
            ['query' => 'nvidia graphics card', 'search_count' => 49, 'click_count' => 20, 'conversion_count' => 3],
            ['query' => 'asus motherboard', 'search_count' => 45, 'click_count' => 18, 'conversion_count' => 4],
            ['query' => 'logitech webcam', 'search_count' => 42, 'click_count' => 16, 'conversion_count' => 5],
        ];

        foreach ($popularSearches as $searchData) {
            PopularSearch::updateOrCreate(
                ['query' => $searchData['query']],
                [
                    'search_count' => $searchData['search_count'],
                    'click_count' => $searchData['click_count'],
                    'conversion_count' => $searchData['conversion_count'],
                    'last_searched_at' => now()->subHours(rand(1, 72)),
                ]
            );
        }

        $this->command->info('Created ' . count($popularSearches) . ' popular searches');
    }
}
