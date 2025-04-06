<?php
namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimpleAdvertisementCreationTest extends DuskTestCase
{
    use DatabaseMigrations;
    public function testAdvertisementFormLoads(): void
    {
        $user = User::factory()->create([
            'user_type' => 'private_advertiser',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->screenshot('home_page_before_nav')
                ->visit('/advertisements/create')
                ->screenshot('advertisements_create');

        });
    }

    public function testAdvertisementPages(): void
    {
        $user = User::factory()->create([
            'user_type' => 'private_advertiser',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->screenshot('home_page')
                ->visit('/advertisements')
                ->screenshot('all_advertisements')
                ->visit('/advertisements/create')
                ->screenshot('adv_create_1')
                ->visit('/advertisements-create')
                ->screenshot('adv_create_2')
                ->visit('/advertisement/create')
                ->screenshot('adv_create_3')
                ->visit('/advertisement/new')
                ->screenshot('adv_create_4');
        });
    }
}
