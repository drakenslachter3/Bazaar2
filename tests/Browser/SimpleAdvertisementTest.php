<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Advertisement;

class SimpleAdvertisementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testHomePageLoads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Bazaar'); 
        });
    }

    public function testAdvertisementsPageLoads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/advertisements')
                    ->assertPathIs('/advertisements');
        });
    }


    public function testUserLogin(): void
    {
        $user = User::factory()->create([
            'email' => 'test1@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'test1@example.com')
                    ->type('password', 'password123')
                    ->press('Login') 
                    ->assertPathIs('/');
        });
    }

    public function testPageScreenshot(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->screenshot('home_page');
            
            $browser->visit('/advertisements')
                    ->screenshot('advertisements_page');
            
            $browser->visit('/login')
                    ->screenshot('login_page');
        });
    }
}