{{-- @extends('layouts.app')

@section('title', 'Resultado do Agente')

@section('stylesheet')
<style>
    .agent-icon {
        font-size: 2rem;
        margin-right: 1rem;
    }
    .result-container {
        background-color: #f8f9fa;
        border: 1px solid #eee;
        border-radius: 5px;
        padding: 20px;
        margin-top: 20px;
    }
    .field-value {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #f1f1f1;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="container-xxl" id="kt_content_container">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h2>Resultado do Agente</h2>
                </div>
                <div class="card-toolbar">
                    <a href="{{ route('agents.show', $agent) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex align-items-center mb-5">
                    <div class="agent-icon">
                        <i class="{{ $agent->icon }}"></i>
                    </div>
                    <div>
                        <h3 class="fs-2x mb-0">{{ $agent->name }}</h3>
                        <p class="text-muted">{{ $agent->description }}</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h3 class="fw-bold m-0">Valores Informados</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($fieldValues as $name => $value)
                                    <div class="field-value">
                                        <strong>{{ $name }}:</strong>
                                        @if (filter_var($value, FILTER_VALIDATE_URL))
                                            <a href="{{ $value }}" target="_blank">{{ basename($value) }}</a>
                                        @else
                                            <p class="mb-0">{{ $value }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0">
                                <div class="card-title">
                                    <h3 class="fw-bold m-0">Resposta</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="result-container">
                                    {!! nl2br(e($result)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


<x-app>
    @slot('stylesheet')
    @endslot

    @section('title', 'Agente')

    @slot('slot')

        @section('stylesheet')
            <style>
                .agent-icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }

                .field-item {
                    padding: 15px;
                    margin-bottom: 15px;
                    border: 1px solid #eee;
                    border-radius: 5px;
                    background-color: #f8f9fa;
                }
            </style>
        @endsection

        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="row gy-5 g-xl-8">

                    <div class="col-xl-4">
                        <div class="card card-flush mb-5">
                            <div class="card-header pt-5 mb-6">
                                <h3 class="card-title align-items-start flex-column">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-40px me-5">
                                            <span class="symbol-label">
                                                <i class="{{ $agent->icon }} fs-2x"></i>
                                            </span>
                                        </div>

                                        <div class="m-0">
                                            <span class="fw-bold text-primary fs-24">{{ $agent->name }}</span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold text-gray-400 d-block fs-11">
                                        {{ $agent->description }}
                                    </span>

                                    <div>
                                        @forelse($agent->categories as $category)
                                            <span class="badge badge-light fs-7 me-1 mt-4">{{ $category->name }}</span>
                                        @empty
                                            <span class="text-muted">Nenhuma categoria atribuída.</span>
                                        @endforelse
                                    </div>
                                </h3>

                                <div class="card-toolbar">

                                </div>
                            </div>

                            <div class="card-body py-0 px-0">

                            </div>
                        </div>

                        <div class="card card-flush h-md-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    Insira as Informações
                                </h3>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('agents.process', $agent) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    @foreach ($agent->fields as $field)
                                        <div class="mb-4">
                                            <label for="field_{{ $field->id }}"
                                                class="form-label {{ $field->required ? 'required' : '' }}">
                                                {{ $field->label }}
                                            </label>

                                            @if ($field->type === 'text')
                                                <input type="text" class="form-control" id="field_{{ $field->id }}"
                                                    name="field_{{ $field->id }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                            @elseif($field->type === 'textarea')
                                                <textarea class="form-control" id="field_{{ $field->id }}" name="field_{{ $field->id }}" rows="3"
                                                    {{ $field->required ? 'required' : '' }}></textarea>
                                            @elseif($field->type === 'select')
                                                <select class="form-select" id="field_{{ $field->id }}"
                                                    name="field_{{ $field->id }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                                    <option value="">Selecione...</option>
                                                    @foreach ($field->options as $option)
                                                        <option value="{{ $option }}">{{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($field->type === 'upload')
                                                <input type="file" class="form-control" id="field_{{ $field->id }}"
                                                    name="field_{{ $field->id }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success text-black">
                                            <i class="fas fa-play text-black"></i> Processar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">

                        <div class="card card-flush h-md-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    Resultado
                                </h3>
                            </div>

                            <div class="card-body">
                                {!! nl2br(e($result)) !!}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    @endslot

    @slot('scripts')
    @endslot
</x-app>
