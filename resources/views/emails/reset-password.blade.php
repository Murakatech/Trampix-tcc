<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2d3748;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #4a5568;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .expiry-notice {
            background-color: #fef5e7;
            border-left: 4px solid #f6ad55;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .expiry-notice p {
            margin: 0;
            color: #744210;
            font-size: 14px;
        }
        .alternative-link {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #4a5568;
        }
        .alternative-link code {
            background-color: #edf2f7;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            word-break: break-all;
            color: #2d3748;
        }
        .footer {
            background-color: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #718096;
        }
        .security-notice {
            background-color: #ebf8ff;
            border-left: 4px solid #3182ce;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .security-notice p {
            margin: 0;
            color: #2c5282;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 30px 20px;
            }
            .reset-button {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Olá!
            </div>
            
            <div class="message">
                Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.
            </div>
            
            <div class="button-container">
                <a href="{{ $actionUrl }}" class="reset-button">
                    Redefinir Senha
                </a>
            </div>
            
            <div class="expiry-notice">
                <p><strong>⏰ Atenção:</strong> Este link de redefinição de senha expirará em 60 minutos.</p>
            </div>
            
            <div class="security-notice">
                <p><strong>🔒 Segurança:</strong> Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária. Sua conta permanece segura.</p>
            </div>
            
            <div class="alternative-link">
                <p><strong>Problemas para clicar no botão?</strong></p>
                <p>Copie e cole o link abaixo no seu navegador:</p>
                <code>{{ $actionUrl }}</code>
            </div>
            
            <div class="message">
                Atenciosamente,<br>
                Equipe {{ config('app.name') }}
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>