<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Como funcionam Categorias e Áreas de Atuação') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">
                        Esta página explica como utilizamos <strong>Categorias</strong> nas vagas e <strong>Áreas de Atuação</strong> nos perfis (Freelancer e Empresa),
                        além de detalhar o fluxo de criação e edição que foi implementado.
                    </p>

                    <hr class="my-4" />

                    <h3 class="text-lg font-semibold mb-2">Categorias (Model: Category)</h3>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>As categorias são usadas exclusivamente em <strong>Vagas</strong> (JobVacancy).</li>
                        <li>Há um campo <code>category_id</code> nas vagas que referencia a tabela <code>categories</code>.</li>
                        <li>Os formulários de criação/edição de vagas agora têm um <strong>select de Categoria</strong> que carrega dados de <code>Category::orderBy('name')</code>.</li>
                        <li>O sistema ainda mantém compatibilidade com o campo legado <code>category</code> (string), convertendo para <code>category_id</code> quando possível.</li>
                        <li>Foi executado um <em>backfill</em> para preencher <code>category_id</code> com base no nome antigo em <code>category</code>, quando há correspondência.</li>
                        <li>Nas listagens e filtros, é possível filtrar por nomes de categorias; o sistema resolve os IDs correspondentes e também considera dados legados.</li>
                    </ul>

                    <h3 class="text-lg font-semibold mb-2">Áreas de Atuação (Model: ActivityArea)</h3>
                    <ul class="list-disc pl-6 space-y-2 mb-4">
                        <li>As áreas de atuação são usadas em <strong>Perfis</strong> (Freelancer e Empresa).</li>
                        <li>Os perfis possuem o campo <code>activity_area_id</code> que referencia a tabela <code>activity_areas</code>.</li>
                        <li>Cada área pertence a um <strong>type</strong>: <code>freelancer</code> ou <code>company</code>, garantindo que cada perfil selecione apenas áreas adequadas ao seu tipo.</li>
                        <li>Os formulários de criação/edição de perfil têm um <strong>select de Área de Atuação</strong>, com validação por tipo e opção de limpar o campo.</li>
                    </ul>

                    <hr class="my-4" />

                    <h3 class="text-lg font-semibold mb-2">Fluxo de Criação e Edição</h3>
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-semibold mb-1">1) Criação de Perfil Freelancer</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Escolher uma <strong>Área de Atuação</strong> (type = <code>freelancer</code>), opcional.</li>
                                <li>Preencher demais informações do perfil e, se disponível, categorias de serviço legadas.</li>
                                <li>Salvar o perfil; é possível editar e limpar a área de atuação depois.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-1">2) Criação de Perfil Empresa</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Escolher uma <strong>Área de Atuação</strong> (type = <code>company</code>), opcional.</li>
                                <li>Preencher dados como nome público, descrição, site, telefone, etc.</li>
                                <li>Marcar se a empresa está ativa (aceita candidaturas).</li>
                                <li>Salvar o perfil; é possível editar e limpar a área de atuação depois.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-1">3) Criação de Vaga</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Disponível para <strong>Empresa</strong> ou <strong>Admin</strong>.</li>
                                <li>Escolher a <strong>Categoria</strong> pelo <code>category_id</code> (lista de <code>categories</code>).</li>
                                <li>Preencher descrição, requisitos, tipo de contratação, tipo de local (Remoto, Híbrido, Presencial) e faixa salarial.</li>
                                <li>Compatibilidade com dados legados: se informado o <code>category</code> (string), o sistema tenta converter para <code>category_id</code>.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-1">4) Edição de Vaga</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Empresa dona da vaga pode editar e alterar a <strong>Categoria</strong> normalmente.</li>
                                <li>O sistema invalida caches relacionados para refletir rapidamente as mudanças.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-semibold mb-1">5) Filtros e Listagens</h4>
                            <ul class="list-disc pl-6 space-y-1">
                                <li>Listagens públicas de vagas permitem filtrar por nomes de categorias; os nomes são mapeados para <code>category_id</code> automaticamente.</li>
                                <li>A listagem de vagas da empresa também aceita filtro por categoria e outros campos (status, contrato, localização).</li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4" />

                    <h3 class="text-lg font-semibold mb-2">Referências Técnicas</h3>
                    <ul class="list-disc pl-6 space-y-1">
                        <li><strong>Models:</strong> <code>App\\Models\\Category</code>, <code>App\\Models\\ActivityArea</code>, <code>App\\Models\\JobVacancy</code>, <code>App\\Models\\Freelancer</code>, <code>App\\Models\\Company</code>.</li>
                        <li><strong>Controllers principais:</strong> <code>JobVacancyController</code>, <code>CompanyVacancyController</code>, <code>ProfileController</code>, <code>FreelancerController</code>, <code>CompanyController</code>.</li>
                        <li><strong>Views atualizadas:</strong> <code>resources/views/vagas/create.blade.php</code>, <code>resources/views/vagas/edit.blade.php</code>, <code>resources/views/profile/edit.blade.php</code>, <code>resources/views/freelancers/create.blade.php</code>, <code>resources/views/companies/create.blade.php</code>.</li>
                        <li><strong>Migrations novas:</strong> <code>add_activity_area_id_to_freelancers</code>, <code>add_activity_area_id_to_companies</code>, <code>add_category_id_to_job_vacancies</code>, <code>backfill_category_id_in_job_vacancies</code>.</li>
                        <li><strong>Seeders:</strong> <code>CategorySeeder</code>, <code>ActivityAreaSeeder</code>.</li>
                    </ul>

                    <div class="mt-6">
                        <p class="text-sm text-gray-600">Esta página é pública e não requer login.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>