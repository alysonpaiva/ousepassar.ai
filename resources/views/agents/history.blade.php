@extends('components.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Histórico de Interações - {{ $agent->name }}</h5>
                        <a href="{{ route('agents.show', $agent) }}" class="btn btn-sm btn-primary">Voltar ao Agente</a>
                    </div>
                </div>

                <div class="card-body">
                    @if($history->isEmpty())
                        <div class="alert alert-info">
                            Nenhuma interação registrada para este agente.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Prompt</th>
                                        <th>Resultado (Prévia)</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ $item->prompt }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ Str::limit($item->result, 100) }}
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('agents.history.detail', $item) }}" class="btn btn-sm btn-info">
                                                    Ver Detalhes
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
