# API Documentation - Sistema de Perfil Dinâmico

## Visão Geral

Esta documentação descreve as APIs REST criadas para o sistema de atualização dinâmica de perfil do Trampix. Todas as APIs requerem autenticação e seguem os padrões REST.

## Base URL
```
http://localhost:8000/api
```

## Autenticação

Todas as APIs requerem que o usuário esteja autenticado. A autenticação é feita através do sistema de sessões do Laravel.

### Headers Obrigatórios
```http
Cookie: laravel_session=<session_token>
X-CSRF-TOKEN: <csrf_token>
```

### Middleware Aplicado
- `auth`: Verifica se o usuário está autenticado
- `web`: Aplica proteções CSRF e gerenciamento de sessão

## Endpoints

### 1. Check Profile Updates

Verifica se há atualizações no perfil do usuário desde a última verificação.

#### Request
```http
GET /api/profile/check-updates
```

#### Headers Opcionais
```http
If-Modified-Since: Sat, 02 Nov 2024 03:30:00 GMT
```

#### Response (200 OK)
```json
{
  "has_updates": true,
  "last_modified": "Sat, 02 Nov 2024 03:30:00 GMT",
  "profile_photo_url": "http://localhost:8000/storage/photos/user.jpg"
}
```

#### Response (304 Not Modified)
Retornado quando o header `If-Modified-Since` indica que o cliente já possui a versão mais recente.

```http
HTTP/1.1 304 Not Modified
Cache-Control: must-revalidate, no-cache, private
Last-Modified: Sat, 02 Nov 2024 03:30:00 GMT
```

#### Response (401 Unauthorized)
```json
{
  "error": "Unauthorized"
}
```

#### Headers de Resposta
```http
Cache-Control: must-revalidate, no-cache, private
Last-Modified: Sat, 02 Nov 2024 03:30:00 GMT
```

### 2. Get Profile Data

Retorna dados completos do perfil do usuário autenticado.

#### Request
```http
GET /api/profile/data
```

#### Response (200 OK)
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

#### Response (401 Unauthorized)
```json
{
  "error": "Unauthorized"
}
```

## Detalhes dos Campos

### User Object
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | integer | ID único do usuário |
| `name` | string | Nome completo do usuário |
| `email` | string | Email do usuário |
| `role` | string | Role do usuário (`freelancer`, `company`, `admin`) |
| `profile_photo_url` | string\|null | URL completa da foto de perfil ou null |
| `initials` | string | Iniciais geradas automaticamente do nome |

### Profile Photo URL
A URL da foto de perfil é determinada pela seguinte ordem de prioridade:
1. Foto no perfil de empresa (`companies.profile_photo`)
2. Foto no perfil de freelancer (`freelancers.profile_photo`)
3. `null` se nenhuma foto estiver disponível

### Initials Generation
As iniciais são geradas seguindo estas regras:
- **Nome único**: Primeira letra (ex: "João" → "J")
- **Múltiplas palavras**: Primeira letra da primeira e última palavra (ex: "João Silva" → "JS")
- **Nome vazio**: Retorna "?"
- **Caracteres especiais**: São removidos antes do processamento

## Rate Limiting

### Limites Aplicados
- **Máximo**: 2 requisições por minuto por usuário
- **Janela**: 60 segundos
- **Comportamento**: Requisições excedentes são ignoradas pelo cliente

### Headers de Rate Limiting
```http
X-RateLimit-Limit: 2
X-RateLimit-Remaining: 1
X-RateLimit-Reset: 1699000000
```

## Caching e Otimização

### HTTP Caching
O sistema utiliza headers HTTP padrão para otimização:

#### If-Modified-Since
```http
If-Modified-Since: Sat, 02 Nov 2024 03:30:00 GMT
```

#### Last-Modified
```http
Last-Modified: Sat, 02 Nov 2024 03:30:00 GMT
```

#### Cache-Control
```http
Cache-Control: must-revalidate, no-cache, private
```

### Estratégia de Cache
1. **Cliente envia** `If-Modified-Since` com timestamp da última atualização
2. **Servidor compara** com `updated_at` do usuário
3. **Se não modificado**: Retorna 304 Not Modified
4. **Se modificado**: Retorna 200 com novos dados

## Códigos de Status

| Código | Descrição | Quando Ocorre |
|--------|-----------|---------------|
| 200 | OK | Requisição bem-sucedida |
| 304 | Not Modified | Dados não foram modificados |
| 401 | Unauthorized | Usuário não autenticado |
| 429 | Too Many Requests | Rate limit excedido |
| 500 | Internal Server Error | Erro interno do servidor |

## Exemplos de Uso

### JavaScript (Fetch API)
```javascript
// Verificar atualizações
async function checkUpdates() {
    const lastModified = localStorage.getItem('lastModified');
    const headers = {};
    
    if (lastModified) {
        headers['If-Modified-Since'] = lastModified;
    }
    
    try {
        const response = await fetch('/api/profile/check-updates', {
            headers,
            credentials: 'same-origin'
        });
        
        if (response.status === 304) {
            console.log('Perfil não modificado');
            return false;
        }
        
        if (response.ok) {
            const data = await response.json();
            localStorage.setItem('lastModified', data.last_modified);
            return data.has_updates;
        }
    } catch (error) {
        console.error('Erro ao verificar atualizações:', error);
    }
    
    return false;
}

// Obter dados do perfil
async function getProfileData() {
    try {
        const response = await fetch('/api/profile/data', {
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.user;
        }
    } catch (error) {
        console.error('Erro ao obter dados do perfil:', error);
    }
    
    return null;
}
```

### cURL
```bash
# Verificar atualizações
curl -X GET "http://localhost:8000/api/profile/check-updates" \
  -H "Cookie: laravel_session=<session_token>" \
  -H "If-Modified-Since: Sat, 02 Nov 2024 03:30:00 GMT"

# Obter dados do perfil
curl -X GET "http://localhost:8000/api/profile/data" \
  -H "Cookie: laravel_session=<session_token>"
```

## Tratamento de Erros

### Estrutura de Erro Padrão
```json
{
  "error": "Mensagem de erro",
  "code": "ERROR_CODE",
  "details": {
    "field": "Detalhes específicos"
  }
}
```

### Tipos de Erro Comuns

#### 1. Usuário Não Autenticado
```json
{
  "error": "Unauthorized"
}
```

#### 2. Sessão Expirada
```json
{
  "error": "Session expired",
  "code": "SESSION_EXPIRED"
}
```

#### 3. Rate Limit Excedido
```json
{
  "error": "Too many requests",
  "code": "RATE_LIMIT_EXCEEDED",
  "details": {
    "retry_after": 60
  }
}
```

## Monitoramento e Logs

### Logs de Aplicação
```php
// Logs automáticos no Laravel
Log::info('Profile update check', [
    'user_id' => $user->id,
    'has_updates' => $hasUpdates,
    'response_time' => $responseTime
]);
```

### Métricas Recomendadas
- Tempo de resposta das APIs
- Taxa de cache hit/miss
- Frequência de atualizações por usuário
- Erros por endpoint

## Versionamento

### Versão Atual
- **Versão**: 1.0
- **Data**: Novembro 2024
- **Compatibilidade**: Laravel 12.x

### Política de Versionamento
- **Major**: Mudanças incompatíveis
- **Minor**: Novas funcionalidades compatíveis
- **Patch**: Correções de bugs

## Segurança

### Medidas Implementadas
1. **Autenticação obrigatória** em todos os endpoints
2. **Proteção CSRF** via middleware web
3. **Rate limiting** para prevenir abuso
4. **Validação de dados** em todas as entradas
5. **Sanitização** de outputs

### Headers de Segurança
```http
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
```

## Changelog

### v1.0.0 (Novembro 2024)
- ✅ Endpoint para verificação de atualizações
- ✅ Endpoint para dados do perfil
- ✅ Implementação de cache HTTP
- ✅ Rate limiting
- ✅ Geração automática de iniciais
- ✅ Suporte a múltiplos tipos de perfil

## Roadmap

### v1.1.0 (Planejado)
- 🔄 WebSocket para atualizações em tempo real
- 🔄 Compressão de resposta (gzip)
- 🔄 Paginação para dados grandes
- 🔄 Filtros avançados

### v1.2.0 (Planejado)
- 🔄 GraphQL endpoint
- 🔄 Webhook notifications
- 🔄 Bulk operations
- 🔄 Advanced analytics

## Suporte

Para dúvidas ou problemas com as APIs:

1. **Documentação**: Consulte esta documentação
2. **Logs**: Verifique os logs do Laravel
3. **Debug**: Ative o modo debug no ambiente de desenvolvimento
4. **Testes**: Execute os testes automatizados

### Contato
- **Equipe**: Trampix Development Team
- **Email**: dev@trampix.com
- **Slack**: #trampix-api