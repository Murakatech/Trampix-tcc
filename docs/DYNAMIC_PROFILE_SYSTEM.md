# Sistema de Atualização Dinâmica de Perfil - Trampix

## Visão Geral

O Sistema de Atualização Dinâmica de Perfil é uma funcionalidade avançada que permite atualizações em tempo real do perfil do usuário na interface, incluindo foto de perfil, informações pessoais e opções de navegação baseadas no role do usuário.

## Arquitetura

### Componentes Principais

1. **ProfileUpdater** (`resources/js/profile-updater.js`)
   - Gerencia atualizações automáticas do perfil
   - Implementa polling inteligente com rate limiting
   - Cache local para otimização de performance

2. **SidebarUpdater** (`resources/js/sidebar-updater.js`)
   - Atualiza dinamicamente as opções do sidebar baseado no role
   - Gerencia permissões e visibilidade de menus
   - Suporte a múltiplos perfis (freelancer, company, admin)

3. **ProfilePhotoController** (`app/Http/Controllers/ProfilePhotoController.php`)
   - APIs REST para verificação de atualizações
   - Otimização com headers HTTP (If-Modified-Since)
   - Geração automática de iniciais

4. **Navigation Component** (`resources/views/layouts/navigation.blade.php`)
   - Interface dinâmica de perfil
   - Dropdown com informações do usuário
   - Integração com sistema de autenticação

## APIs Disponíveis

### GET /api/profile/check-updates
Verifica se há atualizações no perfil do usuário.

**Headers de Otimização:**
- `If-Modified-Since`: Para cache condicional

**Resposta (200):**
```json
{
  "has_updates": true,
  "last_modified": "Sat, 02 Nov 2024 03:30:00 GMT",
  "profile_photo_url": "http://localhost:8000/storage/photos/user.jpg"
}
```

**Resposta (304):** Not Modified (sem conteúdo)

### GET /api/profile/data
Retorna dados completos do perfil do usuário.

**Resposta:**
```json
{
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "role": "freelancer",
    "profile_photo_url": "http://localhost:8000/storage/photos/user.jpg",
    "initials": "JS"
  }
}
```

## Funcionalidades

### 1. Atualização Automática de Perfil
- **Polling Inteligente**: Verifica atualizações a cada 30 segundos
- **Rate Limiting**: Máximo 2 requisições por minuto
- **Cache Local**: Evita requisições desnecessárias
- **Fallback Graceful**: Continua funcionando mesmo com falhas de rede

### 2. Sidebar Dinâmico
- **Role-Based**: Menus específicos para cada tipo de usuário
- **Permissões**: Controle granular de acesso
- **Responsivo**: Adaptação automática para diferentes telas

### 3. Interface de Perfil
- **Avatar Dinâmico**: Foto do usuário ou iniciais geradas automaticamente
- **Status Indicator**: Indicador visual de status online
- **Dropdown Menu**: Acesso rápido a configurações e ações

## Configuração

### 1. Rotas
Adicione as rotas de API no `routes/web.php`:

```php
Route::middleware('auth')->group(function () {
    Route::get('/api/profile/check-updates', [ProfilePhotoController::class, 'checkUpdates']);
    Route::get('/api/profile/data', [ProfilePhotoController::class, 'getProfileData']);
});
```

### 2. Meta Tags
Inclua as meta tags no layout principal (`resources/views/layouts/app.blade.php`):

```blade
@auth
<meta name="user-id" content="{{ auth()->user()->id }}">
<meta name="user-name" content="{{ auth()->user()->name }}">
<meta name="user-email" content="{{ auth()->user()->email }}">
<meta name="user-role" content="{{ auth()->user()->role ?? 'freelancer' }}">
<meta name="user-profile-photo" content="{{ auth()->user()->company?->profile_photo ?? auth()->user()->freelancer?->profile_photo ?? '' }}">
@endauth
```

### 3. JavaScript
Inclua os scripts no `resources/js/app.js`:

```javascript
import './profile-updater.js';
import './sidebar-updater.js';
```

## Testes

### Testes PHP
Execute os testes unitários:

```bash
php artisan test tests/Feature/ProfilePhotoControllerTest.php
```

### Testes JavaScript
Execute os testes JavaScript:

```bash
npm test tests/js/profile-updater.test.js
```

## Performance

### Otimizações Implementadas

1. **HTTP Caching**
   - Headers `If-Modified-Since` e `Last-Modified`
   - Resposta 304 Not Modified quando apropriado

2. **Rate Limiting**
   - Máximo 2 requisições por minuto por usuário
   - Backoff exponencial em caso de erro

3. **Cache Local**
   - localStorage para dados do usuário
   - Evita requisições desnecessárias

4. **Lazy Loading**
   - Componentes carregados apenas quando necessário
   - Inicialização sob demanda

## Segurança

### Medidas Implementadas

1. **Autenticação Obrigatória**
   - Todas as APIs requerem usuário autenticado
   - Middleware `auth` aplicado

2. **Validação de Dados**
   - Sanitização de inputs
   - Validação de tipos de dados

3. **Rate Limiting**
   - Proteção contra abuso de APIs
   - Throttling automático

## Troubleshooting

### Problemas Comuns

1. **Perfil não atualiza**
   - Verificar se o usuário está autenticado
   - Confirmar se as rotas estão registradas
   - Verificar console do navegador para erros

2. **Sidebar não carrega opções**
   - Verificar meta tags no HTML
   - Confirmar role do usuário
   - Verificar permissões

3. **Erro 401 nas APIs**
   - Usuário não autenticado
   - Sessão expirada
   - Middleware auth não aplicado

### Debug

Para debug, ative o modo de desenvolvimento:

```javascript
// No console do navegador
localStorage.setItem('profile-debug', 'true');
```

## Roadmap

### Próximas Funcionalidades

1. **WebSocket Integration**
   - Atualizações em tempo real via WebSocket
   - Redução do polling

2. **Offline Support**
   - Service Worker para funcionamento offline
   - Sincronização quando voltar online

3. **Advanced Caching**
   - Redis para cache distribuído
   - Cache de segundo nível

4. **Analytics**
   - Métricas de uso do sistema
   - Performance monitoring

## Contribuição

Para contribuir com o sistema:

1. Fork o repositório
2. Crie uma branch para sua feature
3. Implemente os testes
4. Faça commit das mudanças
5. Abra um Pull Request

## Licença

Este sistema é parte do projeto Trampix e segue a mesma licença do projeto principal.