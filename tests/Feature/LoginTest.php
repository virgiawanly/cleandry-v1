<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test apakah user bisa mengakses halaman login.
     *
     * @return void
     */
    public function test_user_can_visit_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test apakah user yang sudah login tidak bisa mengakses halaman login.
     *
     * @return void
     */
    public function test_user_cannot_visit_login_when_authenticated()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    /**
     * Test apakah user bisa login dengan kredensial yang benar.
     *
     * @return void
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('rahasiabanget'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasiabanget',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test apakah user bisa login dengan email yang salah.
     *
     * @return void
     */
    public function test_user_cannot_login_with_incorrect_email()
    {
        User::factory()->create([
            'password' => bcrypt('inipassword'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'emailngasal@apaaja.com',
            'password' => 'inipassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertGuest();
    }

    /**
     * Test apakah user bisa login dengan password yang salah.
     *
     * @return void
     */
    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('passwordserius'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'passwordngasal',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertGuest();
    }

    /**
     * Test login dengan SQL injection.
     *
     * @return void
     */
    public function test_user_cannot_login_with_sql_injection()
    {
        User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email' => "' or''==",
            'password' => "' or ''=='",
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertGuest();
    }
}
