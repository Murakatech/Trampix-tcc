@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Conectar</h1>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-6"><div class="text-2xl font-bold text-gray-900">Selecione uma vaga para conectar</div></div>
    @if(isset($grouped) && count($grouped))
        @foreach($grouped as $segmentName => $jobs)
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ $segmentName }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($jobs as $job)
                        <div class="trampix-card text-center">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $job->title }}</h3>
                            <div class="mt-4 flex justify-center">
                                <a href="{{ route('connect.index', ['job_id' => $job->id]) }}" class="btn-trampix-company">Conectar</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white border rounded-lg p-6 text-center">
            <p class="text-gray-600">Você não possui vagas ativas.</p>
        </div>
    @endif
</div>
@endsection