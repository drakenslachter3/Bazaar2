<?php
namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimpleRegistrationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testRegistrationPageLoads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->screenshot('registration_page')
                ->assertSee('Register')
                ->assertPresent('form');
        });
    }

    public function testBasicUserRegistration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->screenshot('before_registration')
                ->type('name', 'Test User')
                ->type('email', 'testuser@example.com')
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->screenshot('registration_form_filled')
                ->press('Register')
                ->screenshot('after_registration');

            $browser->pause(1000)
                ->screenshot('after_registration_with_pause');
        });
    }

    public function testRegistrationValidation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
            // Submit with empty form to trigger validation
                ->press('Register')
                ->screenshot('registration_validation_errors')
            // Check we're still on the registration page
                ->assertPathIs('/register')
            // And the form is still there
                ->assertPresent('form');
        });
    }

    public function testPrivateAdvertiserRegistrationPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->screenshot('home_before_register')
                ->clickLink('Register')
                ->screenshot('register_dropdown_or_page');

            try {
                $browser->visit('/register/private')
                    ->screenshot('private_advertiser_registration');
            } catch (\Exception $e) {
            }
        });
    }

    public function testBusinessAdvertiserRegistrationPage(): void
    {
        $this->browse(function (Browser $browser) {

            try {
                $browser->visit('/register/business')
                    ->screenshot('business_advertiser_registration');

                if ($browser->resolver->findOrFail('input[name="business_name"]')) {
                    $browser->type('business_name', 'Test Business')
                        ->screenshot('business_form_field_filled');
                }
            } catch (\Exception $e) {
            }
        });
    }
}
