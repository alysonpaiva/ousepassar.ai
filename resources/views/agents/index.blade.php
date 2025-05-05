<x-app>
    @slot('stylesheet')
    @endslot

    @section('title', 'Lista de Agentes')

    @slot('slot')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="row gy-5 g-xl-8">

                    <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-md-5 mb-xl-10">
                        <div class="card card-flush mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                </div>
                                <div class="card-toolbar">
                                    <a href="{{ route('agents.create') }}" class="btn btn-light-success">
                                        <i class="fas fa-plus"></i> Novo Agente
                                    </a>
                                </div>
                            </div>
                            <div class="card-body py-4">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($agents->isEmpty())
                                    <div class="alert alert-info">
                                        Nenhum agente cadastrado. Clique no botão "Novo Agente" para começar.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                            <thead>
                                                <tr class="fw-bold text-muted">
                                                    <th>Agente</th>
                                                    <th>Categorias</th>
                                                    <th>Campos</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($agents as $agent)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="agent-icon" style="margin: 0 1rem 0 0">
                                                                    <i class="{{ $agent->icon }} fs-2"></i>
                                                                </div>
                                                                <div class="d-flex flex-column">
                                                                    <a href="{{ route('agents.show', $agent) }}"
                                                                        class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $agent->name }}</a>
                                                                    <span
                                                                        class="text-muted fw-semibold text-muted d-block fs-7">{{ Str::limit($agent->description, 100) }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @foreach ($agent->categories as $category)
                                                                <span
                                                                    class="badge badge-light-primary">{{ $category->name }}</span>
                                                            @endforeach
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge badge-light-info">{{ $agent->fields->count() }}
                                                                campos</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <a href="{{ route('agents.show', $agent) }}"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                                    title="Visualizar">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('agents.edit', $agent) }}"
                                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                                    title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('agents.destroy', $agent) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                                                        title="Excluir"
                                                                        onclick="return confirm('Tem certeza que deseja excluir este agente?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
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
