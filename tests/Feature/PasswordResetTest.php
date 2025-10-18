<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake notifications para capturar emails
        Notification::fake();
        
        // Desabilita middleware CSRF para testes
        $this->withoutMiddleware();
        
        // Compartilha variável $errors para as views
        view()->share('errors', session()->get('errors', new \Illuminate\Support\MessageBag()));
    }

    /** @test */
    public function test_password_reset_link_screen_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Esqueceu sua senha?');
        $response->assertSee('Enviar Link de Redefinição');
    }

    /** @test */
    public function test_password_reset_link_can_be_requested_with_valid_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        
        // Verifica se a notificação foi enviada
        Notification::assertSentTo($user, ResetPassword::class);
        
        // Verifica se o token foi salvo na tabela
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_password_reset_link_request_with_invalid_email()
    {
        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(302);
        
        // Verifica que nenhuma notificação foi enviada
        Notification::assertNothingSent();
        
        // Verifica que nenhum token foi criado
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'nonexistent@example.com',
        ]);
    }

    /** @test */
    public function test_password_reset_screen_can_be_rendered_with_valid_token()
    {
        $user = User::factory()->create();
        
        // Cria um token válido
        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->get("/reset-password/{$token}?email={$user->email}");

        $response->assertStatus(200);
        $response->assertSee('Nova Senha');
        $response->assertSee('Confirmar Nova Senha');
        $response->assertSee('Redefinir Senha');
    }

    /** @test */
    public function test_password_can_be_reset_with_valid_token()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        
        // Cria um token válido
        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        
        // Verifica se a senha foi atualizada
        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
        
        // Verifica se o token foi removido
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function test_password_reset_with_invalid_token()
    {
        $user = User::factory()->create();

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        
        // Verifica que a senha não foi alterada
        $user->refresh();
        $this->assertFalse(Hash::check('new-password', $user->password));
    }

    /** @test */
    public function test_password_reset_with_expired_token()
    {
        $user = User::factory()->create();
        
        // Cria um token expirado (mais de 60 minutos)
        $token = 'expired-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61), // Expirado
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        
        // Verifica que a senha não foi alterada
        $user->refresh();
        $this->assertFalse(Hash::check('new-password', $user->password));
    }

    /** @test */
    public function test_password_reset_validation_rules()
    {
        $user = User::factory()->create();
        
        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Teste com senha muito curta
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);

        // Teste com confirmação de senha diferente
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function test_password_reset_link_request_validation()
    {
        // Teste com email inválido
        $response = $this->post('/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);

        // Teste sem email
        $response = $this->post('/forgot-password', []);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_password_reset_redirects_to_login_with_success_message()
    {
        $user = User::factory()->create();
        
        $token = 'valid-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
    }
}