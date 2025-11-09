# Script de Simulacao de Limpeza do Projeto Trampix
# Data: 30/10/2025
# Modo: SIMULACAO (nao remove arquivos)

param(
    [switch]$Execute = $false,
    [switch]$CreateBackup = $false
)

$ProjectRoot = "c:\laragon\www\trampix"
$LogFile = "$ProjectRoot\cleanup_log_$(Get-Date -Format 'yyyyMMdd_HHmmss').txt"
$BackupDir = "$ProjectRoot\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"

# Funcao para log
function Write-Log {
    param($Message, $Type = "INFO")
    $Timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $LogEntry = "[$Timestamp] [$Type] $Message"
    Write-Host $LogEntry
    Add-Content -Path $LogFile -Value $LogEntry
}

Write-Log "=== INICIANDO SIMULACAO DE LIMPEZA DO PROJETO TRAMPIX ===" "START"
Write-Log "Projeto: $ProjectRoot"
if ($Execute) {
    Write-Log "Modo: EXECUCAO"
} else {
    Write-Log "Modo: SIMULACAO"
}

# Lista de arquivos para remocao
$FilesToRemove = @()

# 1. Cache PHPUnit
$PHPUnitCache = "$ProjectRoot\.phpunit.result.cache"
if (Test-Path $PHPUnitCache) {
    $Size = (Get-Item $PHPUnitCache).Length
    $FilesToRemove += [PSCustomObject]@{
        Path = $PHPUnitCache
        Reason = "Cache PHPUnit (sera regenerado)"
        Size = $Size
        Type = "Cache"
        Action = "Remove"
    }
}

# 2. Logs grandes (manter estrutura, truncar conteudo)
$LargeLogThreshold = 5MB
Get-ChildItem "$ProjectRoot\storage\logs\*.log" | ForEach-Object {
    if ($_.Length -gt $LargeLogThreshold) {
        $FilesToRemove += [PSCustomObject]@{
            Path = $_.FullName
            Reason = "Log muito grande (>5MB) - sera truncado"
            Size = $_.Length
            Type = "Log"
            Action = "Truncate"
        }
    }
}

# 3. Logs especificos antigos (vazios)
Get-ChildItem "$ProjectRoot\storage\logs\trampix_*.log" | ForEach-Object {
    if ($_.Length -eq 0 -and $_.LastWriteTime -lt (Get-Date).AddDays(-7)) {
        $FilesToRemove += [PSCustomObject]@{
            Path = $_.FullName
            Reason = "Log especifico vazio e antigo (>7 dias)"
            Size = $_.Length
            Type = "Log"
            Action = "Remove"
        }
    }
}

# 4. Backup de configuracao antigo
$ConfigBackup = "$ProjectRoot\config_backup_20251025_152624"
if (Test-Path $ConfigBackup) {
    $Size = (Get-ChildItem $ConfigBackup -Recurse | Measure-Object -Property Length -Sum).Sum
    $FilesToRemove += [PSCustomObject]@{
        Path = $ConfigBackup
        Reason = "Backup de configuracao antigo (25/10/2025)"
        Size = $Size
        Type = "Backup"
        Action = "Remove"
    }
}

# 5. Arquivo de configuracao MySQL redundante
$MySQLConfig = "$ProjectRoot\mysql_config.php"
if (Test-Path $MySQLConfig) {
    $Size = (Get-Item $MySQLConfig).Length
    $FilesToRemove += [PSCustomObject]@{
        Path = $MySQLConfig
        Reason = "Configuracao MySQL redundante (ja existe em config/)"
        Size = $Size
        Type = "Config"
        Action = "Remove"
    }
}

# 6. Script de desenvolvimento
$CheckDataScript = "$ProjectRoot\check_current_data.php"
if (Test-Path $CheckDataScript) {
    $Size = (Get-Item $CheckDataScript).Length
    $FilesToRemove += [PSCustomObject]@{
        Path = $CheckDataScript
        Reason = "Script de desenvolvimento/debug"
        Size = $Size
        Type = "Development"
        Action = "Remove"
    }
}

# Relatorio de simulacao
Write-Log "=== RELATORIO DE SIMULACAO ===" "REPORT"
Write-Log "Total de arquivos identificados para remocao: $($FilesToRemove.Count)"

$TotalSize = 0
$FilesToRemove | ForEach-Object {
    $SizeMB = [math]::Round($_.Size / 1MB, 2)
    $TotalSize += $_.Size
    Write-Log "[$($_.Type)] $($_.Action) - $($_.Path) ($SizeMB MB) - $($_.Reason)" "ITEM"
}

$TotalSizeMB = [math]::Round($TotalSize / 1MB, 2)
Write-Log "Espaco total a ser liberado: $TotalSizeMB MB" "SUMMARY"

# Verificacao de seguranca
Write-Log "=== VERIFICACAO DE SEGURANCA ===" "SECURITY"
$SafetyCheck = $true

# Verificar se algum arquivo essencial seria removido
$EssentialPatterns = @(
    "\.env", "\.git", "composer\.(json|lock)", "package(-lock)?\.json",
    "artisan", "phpunit\.xml", "tailwind\.config\.js", "vite\.config\.js"
)

foreach ($item in $FilesToRemove) {
    $IsEssential = $false
    foreach ($pattern in $EssentialPatterns) {
        if ($item.Path -match $pattern) {
            $IsEssential = $true
            break
        }
    }
    
    if ($IsEssential) {
        Write-Log "AVISO: Arquivo essencial detectado para remocao: $($item.Path)" "WARNING"
        $SafetyCheck = $false
    }
}

if ($SafetyCheck) {
    Write-Log "Verificacao de seguranca APROVADA - Nenhum arquivo essencial sera removido" "SUCCESS"
} else {
    Write-Log "Verificacao de seguranca FALHOU - Arquivos essenciais detectados" "ERROR"
}

# Execucao (se solicitada)
if ($Execute -and $SafetyCheck) {
    Write-Log "=== INICIANDO EXECUCAO DA LIMPEZA ===" "EXECUTE"
    
    # Criar backup se solicitado
    if ($CreateBackup) {
        Write-Log "Criando backup em: $BackupDir" "BACKUP"
        New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
        
        foreach ($item in $FilesToRemove) {
            if (Test-Path $item.Path) {
                $RelativePath = $item.Path.Replace($ProjectRoot, "")
                $BackupPath = Join-Path $BackupDir $RelativePath
                $BackupParent = Split-Path $BackupPath -Parent
                
                if (!(Test-Path $BackupParent)) {
                    New-Item -ItemType Directory -Path $BackupParent -Force | Out-Null
                }
                
                Copy-Item $item.Path $BackupPath -Recurse -Force
                Write-Log "Backup criado: $BackupPath" "BACKUP"
            }
        }
    }
    
    # Executar remocoes
    foreach ($item in $FilesToRemove) {
        try {
            if ($item.Action -eq "Truncate") {
                # Truncar log mantendo estrutura
                Clear-Content $item.Path
                Write-Log "Log truncado: $($item.Path)" "SUCCESS"
            } else {
                # Remover arquivo/diretorio
                Remove-Item $item.Path -Recurse -Force
                Write-Log "Removido: $($item.Path)" "SUCCESS"
            }
        } catch {
            Write-Log "Erro ao processar $($item.Path): $($_.Exception.Message)" "ERROR"
        }
    }
    
    Write-Log "=== LIMPEZA CONCLUIDA ===" "COMPLETE"
} elseif ($Execute -and !$SafetyCheck) {
    Write-Log "EXECUCAO CANCELADA - Falha na verificacao de seguranca" "ERROR"
} else {
    Write-Log "=== SIMULACAO CONCLUIDA ===" "COMPLETE"
    Write-Log "Para executar a limpeza, use: .\cleanup_simulation.ps1 -Execute" "INFO"
    Write-Log "Para criar backup antes da limpeza, use: .\cleanup_simulation.ps1 -Execute -CreateBackup" "INFO"
}

Write-Log "Log salvo em: $LogFile" "INFO"
Write-Log "Analise detalhada disponivel em: project_cleanup_analysis.md" "INFO"