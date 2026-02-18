<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Review\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private array $reviewTitles = [
        5 => [
            'Excellent product!',
            'Highly recommended!',
            'Best purchase ever!',
            'Amazing quality!',
            'Worth every taka!',
            'Outstanding performance!',
            'Perfect for my needs!',
            'Exceeded expectations!',
        ],
        4 => [
            'Very good product',
            'Great value for money',
            'Really satisfied',
            'Good quality',
            'Performs well',
            'Happy with purchase',
            'Nice product',
            'Solid choice',
        ],
        3 => [
            'Decent product',
            'It\'s okay',
            'Average performance',
            'Nothing special',
            'Does the job',
            'Fair enough',
            'Could be better',
            'Meets basic needs',
        ],
        2 => [
            'Not impressed',
            'Below expectations',
            'Could be better',
            'Disappointed',
            'Not recommended',
            'Quality issues',
            'Not worth the price',
        ],
        1 => [
            'Very poor quality',
            'Complete waste of money',
            'Don\'t buy this',
            'Terrible experience',
            'Extremely disappointed',
        ],
    ];

    private array $reviewComments = [
        5 => [
            'I\'ve been using this for a month now and it works flawlessly. The build quality is excellent and performance is top-notch. Highly recommend this to anyone looking for quality.',
            'This is exactly what I was looking for. Fast delivery, genuine product, and excellent customer service. Will definitely buy again from this store.',
            'Best decision I made this year. The product performs even better than advertised. Very happy with my purchase!',
            'Outstanding product! Build quality is premium and it works exactly as described. Great value for the price.',
            'Perfect for gaming and productivity. No complaints whatsoever. The performance is incredible!',
        ],
        4 => [
            'Good product overall. Works as expected and the quality is decent. Would have given 5 stars but delivery took a bit longer than expected.',
            'Pretty satisfied with this purchase. It does what it\'s supposed to do and the price was reasonable.',
            'Nice product with good features. Minor improvements could be made but overall a solid choice.',
            'Happy with the purchase. Good build quality and performs well for my daily tasks.',
            'Works great for my needs. Good value for money. Recommended.',
        ],
        3 => [
            'It\'s an average product. Does the basic job but nothing exceptional. You get what you pay for.',
            'Okay for the price. Not the best in the market but decent enough for basic use.',
            'Mixed feelings about this. Some features work great, others not so much. Average overall.',
            'It works but I expected better at this price point. Decent for casual use.',
            'Nothing special but nothing terrible either. Just an average product.',
        ],
        2 => [
            'Not what I expected. The quality doesn\'t match the price. Would not recommend.',
            'Had some issues with the product. Customer service was helpful but still disappointed.',
            'Below my expectations. The build quality could be much better.',
            'Not satisfied with this purchase. The product feels cheap and underperforms.',
        ],
        1 => [
            'Complete waste of money. The product stopped working after just one week. Very disappointed.',
            'Terrible quality. Do not buy this product. I wish I had read reviews before purchasing.',
            'Extremely poor quality. The product doesn\'t match the description at all.',
        ],
    ];

    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please run UserSeeder and ProductSeeder first');
            return;
        }

        $reviewCount = 0;

        // Each product gets 0-5 reviews
        foreach ($products as $product) {
            $numReviews = rand(0, 5);
            $reviewers = $customers->random(min($numReviews, $customers->count()));

            foreach ($reviewers as $customer) {
                // Check if already reviewed
                $exists = Review::where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Weighted random rating (more likely to be 4-5)
                $rating = $this->getWeightedRating();
                
                $review = Review::create([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'order_id' => null,
                    'rating' => $rating,
                    'title' => $this->reviewTitles[$rating][array_rand($this->reviewTitles[$rating])],
                    'comment' => $this->reviewComments[$rating][array_rand($this->reviewComments[$rating])],
                    'status' => $this->getWeightedStatus(),
                    'helpful_count' => rand(0, 20),
                    'not_helpful_count' => rand(0, 5),
                    'is_verified_purchase' => rand(0, 1),
                    'admin_response' => rand(0, 5) === 0 ? 'Thank you for your feedback! We appreciate your review.' : null,
                    'admin_response_at' => rand(0, 5) === 0 ? now()->subDays(rand(1, 30)) : null,
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);

                $reviewCount++;
            }
        }

        $this->command->info("Reviews seeded: {$reviewCount} total");
    }

    private function getWeightedRating(): int
    {
        // 40% chance of 5, 30% of 4, 15% of 3, 10% of 2, 5% of 1
        $rand = rand(1, 100);
        
        if ($rand <= 40) return 5;
        if ($rand <= 70) return 4;
        if ($rand <= 85) return 3;
        if ($rand <= 95) return 2;
        return 1;
    }

    private function getWeightedStatus(): string
    {
        // 80% approved, 15% pending, 5% rejected
        $rand = rand(1, 100);
        
        if ($rand <= 80) return Review::STATUS_APPROVED;
        if ($rand <= 95) return Review::STATUS_PENDING;
        return Review::STATUS_REJECTED;
    }
}
