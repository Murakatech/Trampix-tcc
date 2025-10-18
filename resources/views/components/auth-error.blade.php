@props(['messages', 'allErrors' => null])

@php
    $errorMessages = [];
    
    // Se allErrors for fornecido, coletamos todos os erros
    if ($allErrors) {
        foreach ($allErrors->all() as $error) {
            $errorMessages[] = $error;
        }
    } else {
        // Caso contrário, usamos apenas as mensagens fornecidas
        foreach ((array) $messages as $message) {
            $errorMessages[] = $message;
        }
    }
@endphp

@if (!empty($errorMessages))
    <div {{ $attributes->merge(['class' => 'bg-red-50 border border-red-200 rounded-lg p-4 mb-4']) }}>
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Erro de Autenticação
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errorMessages as $message)
                            <li class="flex items-center">
                                <span class="mr-2">•</span>
                                {{ $message }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif