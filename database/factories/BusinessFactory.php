<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessName = $this->faker->company();
        $customUrl = Str::slug($businessName);
        
        return [
            'name' => $businessName,
            'user_id' => function () {
                return User::factory()->create([
                    'user_type' => 'business_advertiser'
                ])->id;
            },
            'custom_url' => $customUrl . '-' . Str::random(5),
            'theme_settings' => json_encode([
                'primary_color' => $this->faker->hexColor(),
                'secondary_color' => $this->faker->hexColor(),
                'font' => $this->faker->randomElement(['Arial', 'Helvetica', 'Roboto', 'Open Sans']),
            ]),
            'logo_path' => null,
            'contract_path' => null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Configure the factory to create a business for a specific user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser(User $user): Factory
    {
        return $this->state(function (array $attributes) use ($user) {
            // Update user type if it's not already a business advertiser
            if ($user->user_type !== 'business_advertiser') {
                $user->update(['user_type' => 'business_advertiser']);
            }
            
            return [
                'user_id' => $user->id,
            ];
        });
    }

    /**
     * Configure the factory to create a business with an approved contract.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withApprovedContract(): Factory
    {
        return $this->state(function (array $attributes) {
            $contractPath = 'contracts/test-contract-' . Str::random(8) . '.pdf';
            
            // Update the user's contract_approved status
            $userId = $attributes['user_id'];
            if (is_callable($userId)) {
                $userId = $userId();
            }
            
            User::where('id', $userId)->update(['contract_approved' => true]);
            
            return [
                'contract_path' => $contractPath,
            ];
        });
    }

    /**
     * Configure the factory to create a business with a logo.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withLogo(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'logo_path' => 'logos/test-logo-' . Str::random(8) . '.png',
            ];
        });
    }
}