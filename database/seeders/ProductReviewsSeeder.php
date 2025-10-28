<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = DB::table('products')->limit(10)->get();
        $customers = DB::table('customers')->limit(20)->get();
        $users = DB::table('users')->limit(1)->get();

        if ($products->count() === 0 || $customers->count() === 0) {
            echo "No products or customers found. Please seed products and customers first.\n";
            return;
        }

        $reviewTitles = [
            'Excellent Product!',
            'Very Satisfied',
            'Good Quality',
            'Worth the Price',
            'Highly Recommended',
            'Amazing Product',
            'Great Purchase',
            'Love It!',
            'Fantastic Quality',
            'Perfect!',
            'Disappointed',
            'Not as Expected',
            'Could be Better',
            'Average Product',
            'Decent Quality',
        ];

        $positiveComments = [
            'This product exceeded my expectations! The quality is outstanding and it works perfectly.',
            'I am very happy with this purchase. Fast shipping and excellent packaging.',
            'Great value for money. Would definitely recommend to friends and family.',
            'The product arrived quickly and in perfect condition. Very satisfied with the quality.',
            'Exactly as described. Very pleased with this purchase and will buy again.',
            'Outstanding quality and great customer service. Highly recommended!',
            'Best purchase I have made in a while. The product is fantastic!',
            'Very impressed with the quality and durability. Worth every penny.',
        ];

        $neutralComments = [
            'The product is okay. It does what it is supposed to do.',
            'Decent quality for the price. Nothing special but functional.',
            'Average product. No complaints but nothing extraordinary either.',
            'It is alright. Works as expected but could be improved.',
            'Fair quality. Good for basic needs.',
        ];

        $negativeComments = [
            'Not what I expected. The quality is lower than advertised.',
            'Disappointed with this purchase. Would not recommend.',
            'The product arrived damaged. Very unsatisfied.',
            'Does not work as described. Waste of money.',
            'Poor quality. Looking for a refund.',
        ];

        $adminResponses = [
            'Thank you for your positive feedback! We are glad you are satisfied with your purchase.',
            'We appreciate your review! Our team works hard to ensure quality products and service.',
            'Thank you for choosing our product! We are happy you had a great experience.',
            'We appreciate your honest feedback and will use it to improve our products.',
            'We are sorry to hear about your experience. Please contact our support team for assistance.',
            'Thank you for bringing this to our attention. We will investigate and make improvements.',
        ];

        $createdCount = 0;

        foreach ($products as $product) {
            // Each product gets 2-5 reviews
            $reviewCount = rand(2, 5);

            for ($i = 0; $i < $reviewCount; $i++) {
                $customer = $customers->random();
                $rating = rand(1, 5);

                // Select comment based on rating
                if ($rating >= 4) {
                    $comment = $positiveComments[array_rand($positiveComments)];
                    $title = $reviewTitles[rand(0, 9)];
                } elseif ($rating === 3) {
                    $comment = $neutralComments[array_rand($neutralComments)];
                    $title = $reviewTitles[rand(10, 14)];
                } else {
                    $comment = $negativeComments[array_rand($negativeComments)];
                    $title = $reviewTitles[rand(10, 14)];
                }

                $isApproved = rand(0, 100) > 20; // 80% approval rate
                $hasResponse = $isApproved && rand(0, 100) > 50; // 50% of approved get response

                $reviewData = [
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'order_id' => null,
                    'rating' => $rating,
                    'title' => $title,
                    'comment' => $comment,
                    'images' => null,
                    'is_verified_purchase' => rand(0, 100) > 30, // 70% verified
                    'is_approved' => $isApproved,
                    'approved_at' => $isApproved ? now()->subDays(rand(1, 30)) : null,
                    'approved_by' => $isApproved && $users->count() > 0 ? $users->first()->id : null,
                    'helpful_count' => rand(0, 50),
                    'not_helpful_count' => rand(0, 10),
                    'admin_response' => $hasResponse ? $adminResponses[array_rand($adminResponses)] : null,
                    'responded_at' => $hasResponse ? now()->subDays(rand(1, 20)) : null,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now(),
                ];

                try {
                    DB::table('product_reviews')->insert($reviewData);
                    $createdCount++;
                } catch (\Exception $e) {
                    // Skip if customer already reviewed this product
                    continue;
                }
            }
        }

        echo "✓ Created {$createdCount} product reviews\n";
        echo "\nProduct reviews seeded successfully!\n";
    }
}
