<x-app>

    @section('title', 'Configurações')

    @section('stylesheet')
    @endsection

    @slot('slot')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ route('dashboard') }}" class="btn btn-light-success">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0">

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('config.salvar') }}" method="POST">
                            @csrf

                            <div class="mb-10">
                                <label for="APP_NAME" class="form-label required">Nome da Aplicação</label>
                                <input type="text" class="form-control form-control-solid" id="APP_NAME" name="APP_NAME"
                                    value="{{ $app_name }}" required>
                            </div>

                            <div class="mb-10">
                                <label for="APP_ENV" class="form-label required">Status da Aplicação</label>
                                <select class="form-select form-select-solid" id="APP_ENV" name="APP_ENV">
                                    <option {{ $app_env == 'production' ? 'selected' : '' }} value="production">Produção
                                    </option>
                                    <option {{ $app_env == 'local' ? 'selected' : '' }} value="local">Desenvolvimento
                                    </option>
                                </select>
                            </div>

                            <div class="mb-10">
                                <label for="OPENAI_API_KEY" class="form-label required">OPENAI API KEY</label>
                                <input type="text" class="form-control form-control-solid" id="OPENAI_API_KEY"
                                    name="OPENAI_API_KEY" value="{{ $openai_key }}" required>
                            </div>

                            <div class="mb-10">
                                <label for="OPENAI_ASSISTENTE" class="form-label required">OPENAI ASSISTENTE</label>
                                <textarea class="form-control form-control-solid" id="OPENAI_ASSISTENTE" name="OPENAI_ASSISTENTE">{{ $openai_assistente }}</textarea>
                            </div>

                            <div class="mb-10">
                                <label for="GURU_ACCOUNT_TOKEN" class="form-label required">GURU API KEY</label>
                                <input type="text" class="form-control form-control-solid" id="GURU_ACCOUNT_TOKEN"
                                    name="GURU_ACCOUNT_TOKEN" value="{{ $guru_key }}" required>
                            </div>

                            <button type="submit" class="btn btn-light-success mt-3">Salvar Configurações</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    @endslot

    @section('scripts')
    @endsection
</x-app>
