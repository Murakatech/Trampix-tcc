<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Garantir que o locale está definido como 'pt'
        app()->setLocale('pt');

        // Compartilhar a variável $errors com as views para evitar erros
        view()->share('errors', session()->get('errors', new \Illuminate\Support\MessageBag));
    }

    public function test_login_with_invalid_credentials_shows_portuguese_message()
    {
        // Tentar fazer login com credenciais inválidas
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword',
            ]);

        // Verificar se foi redirecionado de volta para o login
        $response->assertRedirect('/login');

        // Verificar se a sessão contém a mensagem de erro em português
        $response->assertSessionHasErrors('email');

        // Verificar se a mensagem específica está em português
        $errors = session('errors');
        $emailErrors = $errors->get('email');

        $this->assertContains('As credenciais informadas estão incorretas.', $emailErrors);
    }

    public function test_login_with_valid_email_but_wrong_password_shows_portuguese_message()
    {
        // Criar um usuário
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        // Tentar fazer login com senha incorreta
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
            ]);

        // Verificar se foi redirecionado de volta para o login
        $response->assertRedirect('/login');

        // Verificar se a sessão contém a mensagem de erro em português
        $response->assertSessionHasErrors('email');

        // Verificar se a mensagem específica está em português
        $errors = session('errors');
        $emailErrors = $errors->get('email');

        $this->assertContains('As credenciais informadas estão incorretas.', $emailErrors);
    }

    public function test_translation_functions_work_correctly()
    {
        // Testar se as funções de tradução estão funcionando
        $this->assertEquals('As credenciais informadas estão incorretas.', __('auth.failed'));
        $this->assertEquals('A senha fornecida está incorreta.', __('auth.password'));
        $this->assertEquals('Muitas tentativas de login. Tente novamente em 60 segundos.', __('auth.throttle', ['seconds' => 60]));
    }

    public function test_locale_is_set_to_portuguese()
    {
        // Verificar se o locale está definido como 'pt'
        $this->assertEquals('pt', app()->getLocale());
    }
}
