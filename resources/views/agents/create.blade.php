<x-app>
    @section('stylesheet')
        <style>
            .icon-preview {
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .icon-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
                gap: 10px;
                max-height: 300px;
                overflow-y: auto;
            }

            .icon-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .icon-item:hover,
            .icon-item.selected {
                background-color: #2B2B40;
                border-color: #2B2B40;
            }

            .icon-item i {
                font-size: 1.5rem;
                margin-bottom: 5px;
            }

            .field-item {
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background-color: #f8f9fa;
            }

            .field-item .handle {
                cursor: move;
                color: #6c757d;
            }

            .field-item .field-actions {
                display: flex;
                gap: 5px;
            }
        </style>
    @endsection

    @section('title', 'Novo Agente')

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
                                    <a href="{{ route('agents.index') }}" class="btn btn-light-success">
                                        <i class="fas fa-arrow-left"></i> Voltar
                                    </a>
                                </div>
                            </div>
                            <div class="card-body py-4">
                                <form action="{{ route('agents.store') }}" method="POST" class="form">
                                    @csrf

                                    <div class="row mb-8">

                                        <div class="col-md-12">

                                            <div class="mb-10">
                                                <label for="name" class="form-label required">Nome</label>
                                                <input type="text"
                                                    class="form-control form-control-solid @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-10">
                                                <label for="description" class="form-label">Descrição</label>
                                                <textarea class="form-control form-control-solid @error('description') is-invalid @enderror" id="description"
                                                    name="description" rows="3">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-10">
                                                <label for="icon" class="form-label required">Ícone</label>
                                                <input type="hidden" id="icon" name="icon"
                                                    value="{{ old('icon', 'fas fa-robot') }} fs-1">
                                                <div class="icon-preview">
                                                    <i id="selected-icon"
                                                        class="{{ old('icon', 'fas fa-robot') }} fs-1"></i>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-light-success"
                                                    data-bs-toggle="modal" data-bs-target="#iconModal">
                                                    Selecionar Ícone
                                                </button>
                                                @error('icon')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-10">
                                                <label for="categories" class="form-label required">Categorias</label>
                                                <select
                                                    class="form-select form-select-solid @error('categories') is-invalid @enderror"
                                                    id="categories" name="categories[]" multiple required
                                                    data-control="select2" data-placeholder="Selecione as categorias">
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('categories')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-10">
                                                <label for="categories" class="form-label required">Campos</label>
                                                <div id="fields-container">
                                                    <div class="alert alert-info">
                                                        Adicione campos dinâmicos para seu agente. Estes campos serão
                                                        preenchidos pelos usuários e seus valores poderão ser referenciados
                                                        no prompt usando [nome_do_campo].
                                                    </div>
                                                    <!-- Os campos adicionados aparecerão aqui -->
                                                </div>
                                                <button type="button" class="btn btn-sm btn-light-success"
                                                    data-bs-toggle="modal" data-bs-target="#addFieldModal">
                                                    <i class="fas fa-plus"></i> Adicionar Campo
                                                </button>
                                            </div>

                                            <div class="mb-0">
                                                <label for="prompt" class="form-label required">Prompt</label>
                                                <textarea class="form-control form-control-solid @error('prompt') is-invalid @enderror" id="prompt" name="prompt"
                                                    rows="5" required>{{ old('prompt') }}</textarea>
                                                <div class="form-text">
                                                    Use [nome_do_campo] para referenciar os valores dos campos dinâmicos no
                                                    prompt.
                                                </div>
                                                @error('prompt')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mb-10">
                                        <button type="submit" class="btn btn-warning text-black">
                                            <i class="fas fa-save text-black"></i> Salvar Agente
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- Modal de Seleção de Ícones -->
        <div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="iconModalLabel">Selecionar Ícone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="icon-search"
                                placeholder="Pesquisar ícones...">
                        </div>
                        <div class="icon-grid">
                            <!-- Ícones comuns do Font Awesome -->
                            <div class="icon-item border-0" data-icon="fas fa-robot"><i
                                    class="fas fa-robot"></i><small>robot</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fa-brain"><i
                                    class="fas fa-brain"></i><small>brain</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fa-cogs"><i
                                    class="fas fa-cogs"></i><small>cogs</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fa-microchip"><i
                                    class="fas fa-microchip"></i><small>microchip</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-server"><i
                                    class="fas fa-server"></i><small>server</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-laptop-code"><i
                                    class="fas fa-laptop-code"></i><small>laptop-code</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-code"><i
                                    class="fas fa-code"></i><small>code</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fa-terminal"><i
                                    class="fas fa-terminal"></i><small>terminal</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-database"><i
                                    class="fas fa-database"></i><small>database</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-network-wired"><i
                                    class="fas fa-network-wired"></i><small>network-wired</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-project-diagram"><i
                                    class="fas fa-project-diagram"></i><small>project-diagram</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-sitemap"><i
                                    class="fas fa-sitemap"></i><small>sitemap</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-chart-bar"><i
                                    class="fas fa-chart-bar"></i><small>chart-bar</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-chart-line"><i
                                    class="fas fa-chart-line"></i><small>chart-line</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-chart-pie"><i
                                    class="fas fa-chart-pie"></i><small>chart-pie</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-alt"><i
                                    class="fas fa-file-alt"></i><small>file-alt</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-code"><i
                                    class="fas fa-file-code"></i><small>file-code</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-csv"><i
                                    class="fas fa-file-csv"></i><small>file-csv</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-pdf"><i
                                    class="fas fa-file-pdf"></i><small>file-pdf</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-image"><i
                                    class="fas fa-file-image"></i><small>file-image</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-video"><i
                                    class="fas fa-file-video"></i><small>file-video</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-audio"><i
                                    class="fas fa-file-audio"></i><small>file-audio</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-file-archive"><i
                                    class="fas fa-file-archive"></i><small>file-archive</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-search"><i
                                    class="fas fa-search"></i><small>search</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-graduation-cap"><i
                                    class="fas fa-graduation-cap"></i><small>graduation-cap</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-university"><i
                                    class="fas fa-university"></i><small>university</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-book"><i
                                    class="fas fa-book"></i><small>book</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fa-book-open"><i
                                    class="fas fa-book-open"></i><small>book-open</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-bookmark"><i
                                    class="fas fa-bookmark"></i><small>bookmark</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-chalkboard"><i
                                    class="fas fa-chalkboard"></i><small>chalkboard</small></div>
                            <div class="icon-item border-0" data-icon="fas fa-chalkboard-teacher"><i
                                    class="fas fa-chalkboard-teacher"></i><small>chalkboard-teacher</small></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-light-success" id="select-icon"
                            data-bs-dismiss="modal">Selecionar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Adicionar Campo -->
        <div class="modal fade" id="addFieldModal" tabindex="-1" aria-labelledby="addFieldModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFieldModalLabel">Adicionar Campo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="field_name" class="form-label required">Nome do Campo</label>
                            <input type="text" class="form-control" id="field_name" name="field_name" required>
                            <div class="form-text">
                                Use apenas letras, números e underscore (_). Este nome será usado para referenciar o campo
                                no
                                prompt como [nome_do_campo].
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="field_label" class="form-label required">Label do Campo</label>
                            <input type="text" class="form-control" id="field_label" name="field_label" required>
                            <div class="form-text">
                                Este texto será exibido para o usuário como label do campo.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="field_type" class="form-label required">Tipo do Campo</label>
                            <select class="form-select" id="field_type" name="field_type" required>
                                <option value="text">Texto (input)</option>
                                <option value="textarea">Área de Texto (textarea)</option>
                                <option value="select">Seleção (select)</option>
                                <option value="upload">Upload de Arquivo</option>
                                <option value="date">Data</option>
                            </select>
                        </div>

                        <div class="mb-3" id="options_container" style="display: none;">
                            <label for="field_options" class="form-label">Opções</label>
                            <div id="options_list">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="field_options[]"
                                        placeholder="Opção">
                                    <button type="button" class="btn btn-outline-danger remove-option">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="add_option">
                                <i class="fas fa-plus"></i> Adicionar Opção
                            </button>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="field_required" name="field_required"
                                value="1">
                            <label class="form-check-label" for="field_required">Campo Obrigatório</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="add_field_btn">Adicionar</button>
                    </div>
                </div>
            </div>
        </div>
    @endslot

    @section('scripts')
        <!-- Sortable.js -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            $(document).ready(function() {

                // Inicializar Select2
                $('#categories').select2();

                // Seleção de ícone
                let selectedIcon = $('#icon').val();

                $('.icon-item').click(function() {
                    $('.icon-item').removeClass('selected');
                    $(this).addClass('selected');
                    selectedIcon = $(this).data('icon');
                });

                $('#select-icon').click(function() {
                    if (selectedIcon) {
                        $('#icon').val(selectedIcon);
                        $('#selected-icon').attr('class', selectedIcon);
                    }
                });

                // Pesquisa de ícones
                $('#icon-search').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    $('.icon-item').each(function() {
                        const iconName = $(this).data('icon').toLowerCase();
                        const iconText = $(this).find('small').text().toLowerCase();
                        if (iconName.includes(searchTerm) || iconText.includes(searchTerm)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });

                // Marcar o ícone atual como selecionado quando o modal é aberto
                $('#iconModal').on('shown.bs.modal', function() {
                    const currentIcon = $('#icon').val();
                    $('.icon-item').removeClass('selected');
                    $(`.icon-item[data-icon="${currentIcon}"]`).addClass('selected');
                });

                // Mostrar/esconder opções para campo select
                $('#field_type').change(function() {
                    if ($(this).val() === 'select') {
                        $('#options_container').show();
                    } else {
                        $('#options_container').hide();
                    }
                });

                // Adicionar opção
                $('#add_option').click(function() {
                    $('#options_list').append(`
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="field_options[]" placeholder="Opção">
                            <button type="button" class="btn btn-outline-danger remove-option">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                });

                // Remover opção
                $(document).on('click', '.remove-option', function() {
                    $(this).closest('.input-group').remove();
                });

                // Contador para IDs únicos de campos temporários
                let fieldCounter = 0;

                // Adicionar campo dinamicamente
                $('#add_field_btn').click(function() {
                    const name = $('#field_name').val();
                    const label = $('#field_label').val();
                    const type = $('#field_type').val();
                    const required = $('#field_required').is(':checked');

                    // Validação básica
                    if (!name || !label || !type) {
                        alert('Por favor, preencha todos os campos obrigatórios.');
                        return;
                    }

                    // Validar formato do nome (apenas letras, números e underscore)
                    if (!/^[a-zA-Z0-9_]+$/.test(name)) {
                        alert('O nome do campo deve conter apenas letras, números e underscore (_).');
                        return;
                    }

                    let options = [];
                    if (type === 'select') {
                        $('input[name="field_options[]"]').each(function() {
                            const optionValue = $(this).val().trim();
                            if (optionValue) {
                                options.push(optionValue);
                            }
                        });

                        if (options.length === 0) {
                            alert('Por favor, adicione pelo menos uma opção para o campo de seleção.');
                            return;
                        }
                    }

                    // Criar HTML para o campo
                    const fieldId = `temp_field_${fieldCounter++}`;
                    let fieldHtml = `
                        <div class="field-item" id="${fieldId}" data-name="${name}" data-label="${label}" data-type="${type}" data-required="${required}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="handle me-2"><i class="fas fa-grip-vertical"></i></span>
                                    <div>
                                        <strong class="text-black">${label}</strong>  
                                        <span class="badge bg-light-success">${type}</span>
                                        ${required ? '<span class="badge bg-danger">Obrigatório</span>' : ''}
                                        <br>
                                        <small class="text-muted">Nome: [${name}]</small>
                    `;

                    if (type === 'select' && options.length > 0) {
                        fieldHtml += `
                                    <br>
                                    <small class="text-muted">
                                        Opções: ${options.join(', ')}
                                    </small>
                        `;
                    }

                    fieldHtml += `
                                    </div>
                                </div>
                                <div class="field-actions">
                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger text-hover-white delete-field" data-id="${fieldId}">
                                        <i class="fas fa-trash text-hover-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    // Adicionar campo à lista
                    if ($('#fields-container .alert').length) {
                        $('#fields-container').empty();
                    }
                    $('#fields-container').append(fieldHtml);

                    // Adicionar campo oculto ao formulário para envio
                    $('form').append(`<input type="hidden" name="temp_fields[]" value="${JSON.stringify({
                name: name,
                label: label,
                type: type,
                required: required,
                options: options
            })}">`);

                    // Limpar o modal e fechá-lo
                    $('#field_name').val('');
                    $('#field_label').val('');
                    $('#field_type').val('text');
                    $('#field_required').prop('checked', false);
                    $('#options_container').hide();
                    $('#options_list').html(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="field_options[]" placeholder="Opção">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
                    $('#addFieldModal').modal('hide');
                });

                // Excluir campo
                $(document).on('click', '.delete-field', function() {
                    const fieldId = $(this).data('id');
                    $(`#${fieldId}`).remove();

                    // Se não houver mais campos, mostrar a mensagem
                    if ($('#fields-container .field-item').length === 0) {
                        $('#fields-container').html(`
                    <div class="alert alert-info">
                        Adicione campos dinâmicos para seu agente. Estes campos serão preenchidos pelos usuários e seus valores poderão ser referenciados no prompt usando [nome_do_campo].
                    </div>
                `);
                    }
                });

                // Inicializar Sortable.js para reordenação de campos
                const fieldsContainer = document.getElementById('fields-container');
                if (fieldsContainer) {
                    new Sortable(fieldsContainer, {
                        handle: '.handle',
                        animation: 150
                    });
                }
            });
        </script>
    @endsection
</x-app>


{{-- @extends('layouts.app')

@section('title', 'Novo Agente')

@section('stylesheet')
    <style>
        .icon-preview {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
        }

        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .icon-item:hover,
        .icon-item.selected {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }

        .icon-item i {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .field-item {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .field-item .handle {
            cursor: move;
            color: #6c757d;
        }

        .field-item .field-actions {
            display: flex;
            gap: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-xxl" id="kt_content_container">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h2>Novo Agente</h2>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('agents.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('agents.store') }}" method="POST" class="form">
                        @csrf

                        <div class="row mb-8">
                            <div class="col-md-6">
                                <div class="card mb-5 mb-xl-8">
                                    <div class="card-header border-0">
                                        <div class="card-title">
                                            <h3 class="fw-bold m-0">Campos Dinâmicos</h3>
                                        </div>
                                        <div class="card-toolbar">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addFieldModal">
                                                <i class="fas fa-plus"></i> Adicionar Campo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div id="fields-container">
                                            <div class="alert alert-info">
                                                Adicione campos dinâmicos para seu agente. Estes campos serão preenchidos
                                                pelos usuários e seus valores poderão ser referenciados no prompt usando
                                                [nome_do_campo].
                                            </div>
                                            <!-- Os campos adicionados aparecerão aqui -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-5 mb-xl-8">
                                    <div class="card-header border-0">
                                        <div class="card-title">
                                            <h3 class="fw-bold m-0">Informações do Agente</h3>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="mb-5">
                                            <label for="name" class="form-label required">Nome</label>
                                            <input type="text"
                                                class="form-control form-control-solid @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-5">
                                            <label for="description" class="form-label">Descrição</label>
                                            <textarea class="form-control form-control-solid @error('description') is-invalid @enderror" id="description"
                                                name="description" rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-5">
                                            <label for="icon" class="form-label required">Ícone</label>
                                            <input type="hidden" id="icon" name="icon"
                                                value="{{ old('icon', 'fas fa-robot') }}">
                                            <div class="icon-preview">
                                                <i id="selected-icon" class="{{ old('icon', 'fas fa-robot') }}"></i>
                                            </div>
                                            <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                                data-bs-target="#iconModal">
                                                Selecionar Ícone
                                            </button>
                                            @error('icon')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-5">
                                            <label for="categories" class="form-label required">Categorias</label>
                                            <select
                                                class="form-select form-select-solid @error('categories') is-invalid @enderror"
                                                id="categories" name="categories[]" multiple required data-control="select2"
                                                data-placeholder="Selecione as categorias">
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-5">
                                            <label for="prompt" class="form-label required">Prompt</label>
                                            <textarea class="form-control form-control-solid @error('prompt') is-invalid @enderror" id="prompt" name="prompt"
                                                rows="5" required>{{ old('prompt') }}</textarea>
                                            <div class="form-text">
                                                Use [nome_do_campo] para referenciar os valores dos campos dinâmicos no
                                                prompt.
                                            </div>
                                            @error('prompt')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Agente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Seleção de Ícones -->
    <div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="iconModalLabel">Selecionar Ícone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="icon-search" placeholder="Pesquisar ícones...">
                    </div>
                    <div class="icon-grid">
                        <!-- Ícones comuns do Font Awesome -->
                        <div class="icon-item" data-icon="fas fa-robot"><i class="fas fa-robot"></i><small>robot</small>
                        </div>
                        <div class="icon-item" data-icon="fas fa-brain"><i class="fas fa-brain"></i><small>brain</small>
                        </div>
                        <div class="icon-item" data-icon="fas fa-cogs"><i class="fas fa-cogs"></i><small>cogs</small>
                        </div>
                        <div class="icon-item" data-icon="fas fa-microchip"><i
                                class="fas fa-microchip"></i><small>microchip</small></div>
                        <div class="icon-item" data-icon="fas fa-server"><i
                                class="fas fa-server"></i><small>server</small></div>
                        <div class="icon-item" data-icon="fas fa-laptop-code"><i
                                class="fas fa-laptop-code"></i><small>laptop-code</small></div>
                        <div class="icon-item" data-icon="fas fa-code"><i class="fas fa-code"></i><small>code</small>
                        </div>
                        <div class="icon-item" data-icon="fas fa-terminal"><i
                                class="fas fa-terminal"></i><small>terminal</small></div>
                        <div class="icon-item" data-icon="fas fa-database"><i
                                class="fas fa-database"></i><small>database</small></div>
                        <div class="icon-item" data-icon="fas fa-network-wired"><i
                                class="fas fa-network-wired"></i><small>network-wired</small></div>
                        <div class="icon-item" data-icon="fas fa-project-diagram"><i
                                class="fas fa-project-diagram"></i><small>project-diagram</small></div>
                        <div class="icon-item" data-icon="fas fa-sitemap"><i
                                class="fas fa-sitemap"></i><small>sitemap</small></div>
                        <div class="icon-item" data-icon="fas fa-chart-bar"><i
                                class="fas fa-chart-bar"></i><small>chart-bar</small></div>
                        <div class="icon-item" data-icon="fas fa-chart-line"><i
                                class="fas fa-chart-line"></i><small>chart-line</small></div>
                        <div class="icon-item" data-icon="fas fa-chart-pie"><i
                                class="fas fa-chart-pie"></i><small>chart-pie</small></div>
                        <div class="icon-item" data-icon="fas fa-file-alt"><i
                                class="fas fa-file-alt"></i><small>file-alt</small></div>
                        <div class="icon-item" data-icon="fas fa-file-code"><i
                                class="fas fa-file-code"></i><small>file-code</small></div>
                        <div class="icon-item" data-icon="fas fa-file-csv"><i
                                class="fas fa-file-csv"></i><small>file-csv</small></div>
                        <div class="icon-item" data-icon="fas fa-file-pdf"><i
                                class="fas fa-file-pdf"></i><small>file-pdf</small></div>
                        <div class="icon-item" data-icon="fas fa-file-image"><i
                                class="fas fa-file-image"></i><small>file-image</small></div>
                        <div class="icon-item" data-icon="fas fa-file-video"><i
                                class="fas fa-file-video"></i><small>file-video</small></div>
                        <div class="icon-item" data-icon="fas fa-file-audio"><i
                                class="fas fa-file-audio"></i><small>file-audio</small></div>
                        <div class="icon-item" data-icon="fas fa-file-archive"><i
                                class="fas fa-file-archive"></i><small>file-archive</small></div>
                        <div class="icon-item" data-icon="fas fa-search"><i
                                class="fas fa-search"></i><small>search</small></div>
                        <div class="icon-item" data-icon="fas fa-graduation-cap"><i
                                class="fas fa-graduation-cap"></i><small>graduation-cap</small></div>
                        <div class="icon-item" data-icon="fas fa-university"><i
                                class="fas fa-university"></i><small>university</small></div>
                        <div class="icon-item" data-icon="fas fa-book"><i class="fas fa-book"></i><small>book</small>
                        </div>
                        <div class="icon-item" data-icon="fas fa-book-open"><i
                                class="fas fa-book-open"></i><small>book-open</small></div>
                        <div class="icon-item" data-icon="fas fa-bookmark"><i
                                class="fas fa-bookmark"></i><small>bookmark</small></div>
                        <div class="icon-item" data-icon="fas fa-chalkboard"><i
                                class="fas fa-chalkboard"></i><small>chalkboard</small></div>
                        <div class="icon-item" data-icon="fas fa-chalkboard-teacher"><i
                                class="fas fa-chalkboard-teacher"></i><small>chalkboard-teacher</small></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="select-icon"
                        data-bs-dismiss="modal">Selecionar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Campo -->
    <div class="modal fade" id="addFieldModal" tabindex="-1" aria-labelledby="addFieldModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFieldModalLabel">Adicionar Campo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="field_name" class="form-label required">Nome do Campo</label>
                        <input type="text" class="form-control" id="field_name" name="field_name" required>
                        <div class="form-text">
                            Use apenas letras, números e underscore (_). Este nome será usado para referenciar o campo no
                            prompt como [nome_do_campo].
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="field_label" class="form-label required">Label do Campo</label>
                        <input type="text" class="form-control" id="field_label" name="field_label" required>
                        <div class="form-text">
                            Este texto será exibido para o usuário como label do campo.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="field_type" class="form-label required">Tipo do Campo</label>
                        <select class="form-select" id="field_type" name="field_type" required>
                            <option value="text">Texto (input)</option>
                            <option value="textarea">Área de Texto (textarea)</option>
                            <option value="select">Seleção (select)</option>
                            <option value="upload">Upload de Arquivo</option>
                        </select>
                    </div>

                    <div class="mb-3" id="options_container" style="display: none;">
                        <label for="field_options" class="form-label">Opções</label>
                        <div id="options_list">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="field_options[]" placeholder="Opção">
                                <button type="button" class="btn btn-outline-danger remove-option">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="add_option">
                            <i class="fas fa-plus"></i> Adicionar Opção
                        </button>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="field_required" name="field_required"
                            value="1">
                        <label class="form-check-label" for="field_required">Campo Obrigatório</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="add_field_btn">Adicionar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('#categories').select2({
                theme: 'bootstrap-5'
            });

            // Seleção de ícone
            let selectedIcon = $('#icon').val();

            $('.icon-item').click(function() {
                $('.icon-item').removeClass('selected');
                $(this).addClass('selected');
                selectedIcon = $(this).data('icon');
            });

            $('#select-icon').click(function() {
                if (selectedIcon) {
                    $('#icon').val(selectedIcon);
                    $('#selected-icon').attr('class', selectedIcon);
                }
            });

            // Pesquisa de ícones
            $('#icon-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.icon-item').each(function() {
                    const iconName = $(this).data('icon').toLowerCase();
                    const iconText = $(this).find('small').text().toLowerCase();
                    if (iconName.includes(searchTerm) || iconText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Marcar o ícone atual como selecionado quando o modal é aberto
            $('#iconModal').on('shown.bs.modal', function() {
                const currentIcon = $('#icon').val();
                $('.icon-item').removeClass('selected');
                $(`.icon-item[data-icon="${currentIcon}"]`).addClass('selected');
            });

            // Mostrar/esconder opções para campo select
            $('#field_type').change(function() {
                if ($(this).val() === 'select') {
                    $('#options_container').show();
                } else {
                    $('#options_container').hide();
                }
            });

            // Adicionar opção
            $('#add_option').click(function() {
                $('#options_list').append(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="field_options[]" placeholder="Opção">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
            });

            // Remover opção
            $(document).on('click', '.remove-option', function() {
                $(this).closest('.input-group').remove();
            });

            // Contador para IDs únicos de campos temporários
            let fieldCounter = 0;

            // Adicionar campo dinamicamente
            $('#add_field_btn').click(function() {
                const name = $('#field_name').val();
                const label = $('#field_label').val();
                const type = $('#field_type').val();
                const required = $('#field_required').is(':checked');

                // Validação básica
                if (!name || !label || !type) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return;
                }

                // Validar formato do nome (apenas letras, números e underscore)
                if (!/^[a-zA-Z0-9_]+$/.test(name)) {
                    alert('O nome do campo deve conter apenas letras, números e underscore (_).');
                    return;
                }

                let options = [];
                if (type === 'select') {
                    $('input[name="field_options[]"]').each(function() {
                        const optionValue = $(this).val().trim();
                        if (optionValue) {
                            options.push(optionValue);
                        }
                    });

                    if (options.length === 0) {
                        alert('Por favor, adicione pelo menos uma opção para o campo de seleção.');
                        return;
                    }
                }

                // Criar HTML para o campo
                const fieldId = `temp_field_${fieldCounter++}`;
                let fieldHtml = `
                <div class="field-item" id="${fieldId}" data-name="${name}" data-label="${label}" data-type="${type}" data-required="${required}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="handle me-2"><i class="fas fa-grip-vertical"></i></span>
                            <div>
                                <strong>${label}</strong> 
                                <span class="badge bg-secondary">${type}</span>
                                ${required ? '<span class="badge bg-danger">Obrigatório</span>' : ''}
                                <br>
                                <small class="text-muted">Nome: [${name}]</small>
            `;

                if (type === 'select' && options.length > 0) {
                    fieldHtml += `
                                <br>
                                <small class="text-muted">
                                    Opções: ${options.join(', ')}
                                </small>
                `;
                }

                fieldHtml += `
                            </div>
                        </div>
                        <div class="field-actions">
                            <button type="button" class="btn btn-sm btn-outline-danger delete-field" data-id="${fieldId}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

                // Adicionar campo à lista
                if ($('#fields-container .alert').length) {
                    $('#fields-container').empty();
                }
                $('#fields-container').append(fieldHtml);

                // Adicionar campo oculto ao formulário para envio
                $('form').append(`<input type="hidden" name="temp_fields[]" value="${JSON.stringify({
                name: name,
                label: label,
                type: type,
                required: required,
                options: options
            })}">`);

                // Limpar o modal e fechá-lo
                $('#field_name').val('');
                $('#field_label').val('');
                $('#field_type').val('text');
                $('#field_required').prop('checked', false);
                $('#options_container').hide();
                $('#options_list').html(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="field_options[]" placeholder="Opção">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
                $('#addFieldModal').modal('hide');
            });

            // Excluir campo
            $(document).on('click', '.delete-field', function() {
                const fieldId = $(this).data('id');
                $(`#${fieldId}`).remove();

                // Se não houver mais campos, mostrar a mensagem
                if ($('#fields-container .field-item').length === 0) {
                    $('#fields-container').html(`
                    <div class="alert alert-info">
                        Adicione campos dinâmicos para seu agente. Estes campos serão preenchidos pelos usuários e seus valores poderão ser referenciados no prompt usando [nome_do_campo].
                    </div>
                `);
                }
            });

            // Inicializar Sortable.js para reordenação de campos
            const fieldsContainer = document.getElementById('fields-container');
            if (fieldsContainer) {
                new Sortable(fieldsContainer, {
                    handle: '.handle',
                    animation: 150
                });
            }
        });
    </script>
@endsection --}}
