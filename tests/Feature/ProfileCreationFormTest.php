<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCreationFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function freelancer_creation_form_has_display_name_field()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('freelancers.create'));

        $response->assertStatus(200);
        $response->assertSee('name="display_name"', false);
        $response->assertSee('Nome Profissional');
    }

    /** @test */
    public function company_creation_form_has_display_name_field()
    {
        // Este teste verifica se o arquivo de view contém o campo display_name
        $viewContent = file_get_contents(resource_path('views/companies/create.blade.php'));
        
        $this->assertStringContainsString('name="display_name"', $viewContent);
        $this->assertStringContainsString('Nome da Empresa', $viewContent);
    }

    /** @test */
    public function freelancer_can_be_created_with_display_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('freelancers.store'), [
                'display_name' => 'João Silva Dev',
                'bio' => 'Desenvolvedor experiente em Laravel e Vue.js',
                'portfolio_url' => 'https://joaosilva.dev',
                'phone' => '(11) 99999-9999',
                'location' => 'São Paulo, SP',
                'hourly_rate' => 75.00,
                'availability' => 'Disponível para projetos de 20h/semana'
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Perfil de freelancer criado com sucesso!');
        
        $this->assertDatabaseHas('freelancers', [
            'user_id' => $user->id,
            'display_name' => 'João Silva Dev',
            'bio' => 'Desenvolvedor experiente em Laravel e Vue.js'
        ]);
    }

    /** @test */
    public function company_can_be_created_with_display_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('companies.store'), [
                'display_name' => 'Tech Solutions Ltda',
                'description' => 'Empresa de desenvolvimento de software',
                'website' => 'https://techsolutions.com',
                'phone' => '(11) 3333-4444',
                'employees_count' => 25,
                'founded_year' => 2020
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Perfil de empresa criado com sucesso!');
        
        $this->assertDatabaseHas('companies', [
            'user_id' => $user->id,
            'name' => 'Tech Solutions Ltda',
            'display_name' => 'Tech Solutions Ltda'
        ]);
    }

    /** @test */
    public function display_name_is_required_for_freelancer()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('freelancers.store'), [
                'bio' => 'Desenvolvedor experiente'
                // display_name omitido intencionalmente
            ]);

        $response->assertSessionHasErrors('display_name');
    }

    /** @test */
    public function display_name_is_required_for_company()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('companies.store'), [
                'description' => 'Empresa de tecnologia'
                // display_name omitido intencionalmente
            ]);

        $response->assertSessionHasErrors('display_name');
    }
}