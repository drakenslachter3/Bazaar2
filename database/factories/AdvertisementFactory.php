<?php

namespace Database\Factories;

use App\Models\Advertisement;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Advertisement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['sale', 'rent']);
        $price = $type === 'sale' 
            ? $this->faker->randomFloat(2, 10, 1000) 
            : $this->faker->randomFloat(2, 5, 100);

        // Get a user or create one if none exists
        $user = User::factory()->create([
            'user_type' => $this->faker->randomElement(['private_advertiser', 'business_advertiser'])
        ]);

        // Only set business_id if user is a business advertiser
        $businessId = null;
        if ($user->user_type === 'business_advertiser') {
            $business = Business::where('user_id', $user->id)->first();
            if (!$business) {
                $business = Business::factory()->create(['user_id' => $user->id]);
            }
            $businessId = $business->id;
        }

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'type' => $type,
            'user_id' => $user->id,
            'business_id' => $businessId,
            'active' => true,
            'expiry_date' => $this->faker->optional(0.7)->dateTimeBetween('+1 week', '+3 months'),
            'qr_code_path' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Configure the factory to create a for-sale advertisement.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSale(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'sale',
                'price' => $this->faker->randomFloat(2, 10, 1000),
            ];
        });
    }

    /**
     * Configure the factory to create a for-rent advertisement.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forRent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'rent',
                'price' => $this->faker->randomFloat(2, 5, 100),
            ];
        });
    }

    /**
     * Configure the factory to create an expired advertisement.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function expired(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'expiry_date' => $this->faker->dateTimeBetween('-3 months', '-1 day'),
            ];
        });
    }

    /**
     * Configure the factory to create an advertisement posted by a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function byUser(User $user): Factory
    {
        $businessId = null;
        if ($user->user_type === 'business_advertiser') {
            $business = Business::where('user_id', $user->id)->first();
            if ($business) {
                $businessId = $business->id;
            }
        }

        return $this->state(function (array $attributes) use ($user, $businessId) {
            return [
                'user_id' => $user->id,
                'business_id' => $businessId,
            ];
        });
    }

    /**
     * Configure the factory to create an inactive advertisement.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
}