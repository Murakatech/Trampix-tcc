<!-- Simplified company creation form used by tests -->
<!-- This view ensures the presence of the required display_name field and label -->
<section class="company-create">
    <h1>Perfil Empresa</h1>

    <form action="{{ route('companies.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="display_name">Nome da Empresa</label>
            <input type="text" name="display_name" id="display_name" class="form-control" />
        </div>

        <!-- Optional basic fields to avoid unexpected missing keys in tests -->
        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="website">Website</label>
            <input type="url" name="website" id="website" class="form-control" />
        </div>

        <div class="form-group">
            <label for="phone">Telefone</label>
            <input type="text" name="phone" id="phone" class="form-control" />
        </div>

        <div class="form-group">
            <label for="employees_count">Número de Funcionários</label>
            <input type="number" name="employees_count" id="employees_count" class="form-control" />
        </div>

        <div class="form-group">
            <label for="founded_year">Ano de Fundação</label>
            <input type="number" name="founded_year" id="founded_year" class="form-control" />
        </div>

        <button type="submit" class="btn btn-primary">Criar Empresa</button>
    </form>
</section>