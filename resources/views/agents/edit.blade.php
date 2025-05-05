<x-app>

    @section('title', 'Editar Agente')

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

    @slot('slot')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ route('agents.index') }}" class="btn btn-light-success">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('agents.update', $agent) }}" method="POST" class="form">
                            @csrf
                            @method('PUT')

                            <div class="row mb-8">

                                <div class="col-md-12">

                                    <div class="mb-10">
                                        <label for="name" class="form-label required">Nome</label>
                                        <input type="text"
                                            class="form-control form-control-solid @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $agent->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-10">
                                        <label for="description" class="form-label">Descrição</label>
                                        <textarea class="form-control form-control-solid @error('description') is-invalid @enderror" id="description"
                                            name="description" rows="3">{{ old('description', $agent->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-10">
                                        <label for="icon" class="form-label required">Ícone</label>
                                        <input type="hidden" id="icon" name="icon"
                                            value="{{ old('icon', $agent->icon) }} fs-2">
                                        <div class="icon-preview">
                                            <i id="selected-icon" class="{{ old('icon', $agent->icon) }} fs-2"></i>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light-success" data-bs-toggle="modal"
                                            data-bs-target="#iconModal">
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
                                            id="categories" name="categories[]" multiple required data-control="select2"
                                            data-placeholder="Selecione as categorias">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', $agent->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
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
                                            @if ($agent->fields->isEmpty())
                                                <div class="alert alert-info">
                                                    Nenhum campo dinâmico adicionado. Clique no botão "Adicionar
                                                    Campo"
                                                    para
                                                    começar.
                                                </div>
                                            @else
                                                @foreach ($agent->fields as $field)
                                                    <div class="field-item" data-id="{{ $field->id }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center">
                                                                <span class="handle me-2"><i
                                                                        class="fas fa-grip-vertical"></i></span>
                                                                <div>
                                                                    <strong class="text-black">{{ $field->label }}</strong>
                                                                    <span
                                                                        class="badge bg-secondary">{{ $field->type }}</span>
                                                                    @if ($field->required)
                                                                        <span class="badge bg-danger">Obrigatório</span>
                                                                    @endif
                                                                    <br>
                                                                    <small class="text-muted">Nome:
                                                                        [{{ $field->name }}]</small>
                                                                    @if ($field->type === 'select' && !empty($field->options))
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            Opções:
                                                                            {{ implode(', ', $field->options) }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="field-actions">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary edit-field"
                                                                    data-id="{{ $field->id }}"
                                                                    data-name="{{ $field->name }}"
                                                                    data-label="{{ $field->label }}"
                                                                    data-type="{{ $field->type }}"
                                                                    data-required="{{ $field->required ? 'true' : 'false' }}"
                                                                    data-options="{{ json_encode($field->options) }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger delete-field"
                                                                    data-id="{{ $field->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light-success" data-bs-toggle="modal"
                                            data-bs-target="#addFieldModal">
                                            <i class="fas fa-plus"></i> Adicionar Campo
                                        </button>
                                    </div>

                                    <div class="mb-0">
                                        <label for="prompt" class="form-label required">Prompt</label>
                                        <textarea class="form-control form-control-solid @error('prompt') is-invalid @enderror" id="prompt" name="prompt"
                                            rows="5" required>{{ old('prompt', $agent->prompt) }}</textarea>
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

                            <div class="d-flex justify-content-end mb-5">
                                <button type="submit" class="btn btn-warning text-black">
                                    <i class="fas fa-save text-black"></i> Atualizar Agente
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
                            <input type="text" class="form-control" id="icon-search"
                                placeholder="Pesquisar ícones...">
                        </div>
                        <div class="icon-grid">
                            <!-- Ícones comuns do Font Awesome -->
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-robot"><i
                                    class="fas fa-robot"></i><small>robot</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-brain"><i
                                    class="fas fa-brain"></i><small>brain</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-cogs"><i
                                    class="fas fa-cogs"></i><small>cogs</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-microchip"><i
                                    class="fas fa-microchip"></i><small>microchip</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-server"><i
                                    class="fas fa-server"></i><small>server</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-laptop-code"><i
                                    class="fas fa-laptop-code"></i><small>laptop-code</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-code"><i
                                    class="fas fa-code"></i><small>code</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-terminal"><i
                                    class="fas fa-terminal"></i><small>terminal</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-database"><i
                                    class="fas fa-database"></i><small>database</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-network-wired"><i
                                    class="fas fa-network-wired"></i><small>network-wired</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-project-diagram"><i
                                    class="fas fa-project-diagram"></i><small>project-diagram</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-sitemap"><i
                                    class="fas fa-sitemap"></i><small>sitemap</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-chart-bar"><i
                                    class="fas fa-chart-bar"></i><small>chart-bar</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-chart-line"><i
                                    class="fas fa-chart-line"></i><small>chart-line</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-chart-pie"><i
                                    class="fas fa-chart-pie"></i><small>chart-pie</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-alt"><i
                                    class="fas fa-file-alt"></i><small>file-alt</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-code"><i
                                    class="fas fa-file-code"></i><small>file-code</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-csv"><i
                                    class="fas fa-file-csv"></i><small>file-csv</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-pdf"><i
                                    class="fas fa-file-pdf"></i><small>file-pdf</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-image"><i
                                    class="fas fa-file-image"></i><small>file-image</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-video"><i
                                    class="fas fa-file-video"></i><small>file-video</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-audio"><i
                                    class="fas fa-file-audio"></i><small>file-audio</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-file-archive"><i
                                    class="fas fa-file-archive"></i><small>file-archive</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-search"><i
                                    class="fas fa-search"></i><small>search</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-graduation-cap"><i
                                    class="fas fa-graduation-cap"></i><small>graduation-cap</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-university"><i
                                    class="fas fa-university"></i><small>university</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-book"><i
                                    class="fas fa-book"></i><small>book</small>
                            </div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-book-open"><i
                                    class="fas fa-book-open"></i><small>book-open</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-bookmark"><i
                                    class="fas fa-bookmark"></i><small>bookmark</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-chalkboard"><i
                                    class="fas fa-chalkboard"></i><small>chalkboard</small></div>
                            <div class="icon-item border-0" data-icon="fas fs-2 fa-chalkboard-teacher"><i
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
                    <form action="{{ route('fields.store', $agent) }}" method="POST" id="addFieldForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="field_name" class="form-label required">Nome do Campo</label>
                                <input type="text" class="form-control" id="field_name" name="name" required>
                                <div class="form-text">
                                    Use apenas letras, números e underscore (_). Este nome será usado para referenciar o
                                    campo
                                    no prompt como [nome_do_campo].
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="field_label" class="form-label required">Label do Campo</label>
                                <input type="text" class="form-control" id="field_label" name="label" required>
                                <div class="form-text">
                                    Este texto será exibido para o usuário como label do campo.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="field_type" class="form-label required">Tipo do Campo</label>
                                <select class="form-select" id="field_type" name="type" required>
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
                                        <input type="text" class="form-control" name="options[]" placeholder="Opção">
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
                                <input type="checkbox" class="form-check-input" id="field_required" name="required"
                                    value="1">
                                <label class="form-check-label" for="field_required">Campo Obrigatório</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-light-success">Adicionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para Editar Campo -->
        <div class="modal fade" id="editFieldModal" tabindex="-1" aria-labelledby="editFieldModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFieldModalLabel">Editar Campo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editFieldForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_field_name" class="form-label required">Nome do Campo</label>
                                <input type="text" class="form-control" id="edit_field_name" name="name" required>
                                <div class="form-text">
                                    Use apenas letras, números e underscore (_). Este nome será usado para referenciar o
                                    campo
                                    no prompt como [nome_do_campo].
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_field_label" class="form-label required">Label do Campo</label>
                                <input type="text" class="form-control" id="edit_field_label" name="label"
                                    required>
                                <div class="form-text">
                                    Este texto será exibido para o usuário como label do campo.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_field_type" class="form-label required">Tipo do Campo</label>
                                <select class="form-select" id="edit_field_type" name="type" required>
                                    <option value="text">Texto (input)</option>
                                    <option value="textarea">Área de Texto (textarea)</option>
                                    <option value="select">Seleção (select)</option>
                                    <option value="upload">Upload de Arquivo</option>
                                </select>
                            </div>

                            <div class="mb-3" id="edit_options_container" style="display: none;">
                                <label for="edit_field_options" class="form-label">Opções</label>
                                <div id="edit_options_list">
                                    <!-- Opções serão adicionadas dinamicamente -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="edit_add_option">
                                    <i class="fas fa-plus"></i> Adicionar Opção
                                </button>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_field_required" name="required"
                                    value="1">
                                <label class="form-check-label" for="edit_field_required">Campo Obrigatório</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                        </div>
                    </form>
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

                $('#edit_field_type').change(function() {
                    if ($(this).val() === 'select') {
                        $('#edit_options_container').show();
                    } else {
                        $('#edit_options_container').hide();
                    }
                });

                // Adicionar opção
                $('#add_option').click(function() {
                    $('#options_list').append(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="options[]" placeholder="Opção">
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `);
                });

                $('#edit_add_option').click(function() {
                    $('#edit_options_list').append(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="options[]" placeholder="Opção">
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

                // Editar campo
                $('.edit-field').click(function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const label = $(this).data('label');
                    const type = $(this).data('type');
                    const required = $(this).data('required') === 'true';
                    const options = $(this).data('options');

                    $('#edit_field_name').val(name);
                    $('#edit_field_label').val(label);
                    $('#edit_field_type').val(type);
                    $('#edit_field_required').prop('checked', required);

                    $('#edit_options_list').empty();
                    if (type === 'select' && options && options.length > 0) {
                        $('#edit_options_container').show();
                        options.forEach(function(option) {
                            $('#edit_options_list').append(`
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="options[]" placeholder="Opção" value="${option}">
                            <button type="button" class="btn btn-outline-danger remove-option">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                        });
                    } else {
                        $('#edit_options_container').hide();
                        $('#edit_options_list').append(`
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="options[]" placeholder="Opção">
                        <button type="button" class="btn btn-outline-danger remove-option">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                    }

                    $('#editFieldForm').attr('action', `/fields/${id}`);
                    $('#editFieldModal').modal('show');
                });

                // Excluir campo
                $('.delete-field').click(function() {
                    const id = $(this).data('id');
                    if (confirm('Tem certeza que deseja excluir este campo?')) {
                        $.ajax({
                            url: `/fields/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function() {
                                $(`[data-id="${id}"]`).remove();
                                if ($('#fields-container .field-item').length === 0) {
                                    $('#fields-container').html(`
                                <div class="alert alert-info">
                                    Nenhum campo dinâmico adicionado. Clique no botão "Adicionar Campo" para começar.
                                </div>
                            `);
                                }
                            },
                            error: function() {
                                alert('Erro ao excluir o campo. Por favor, tente novamente.');
                            }
                        });
                    }
                });

                // Inicializar Sortable.js para reordenação de campos
                const fieldsContainer = document.getElementById('fields-container');
                if (fieldsContainer) {
                    new Sortable(fieldsContainer, {
                        handle: '.handle',
                        animation: 150,
                        onEnd: function() {
                            // Atualizar a ordem dos campos
                            const fieldIds = [];
                            $('.field-item').each(function(index) {
                                fieldIds.push({
                                    id: $(this).data('id'),
                                    order: index
                                });
                            });

                            $.ajax({
                                url: '{{ route('fields.reorder', $agent) }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    fields: fieldIds
                                },
                                error: function() {
                                    alert(
                                        'Erro ao reordenar os campos. Por favor, tente novamente.'
                                    );
                                }
                            });
                        }
                    });
                }
            });
        </script>
    @endsection
</x-app>
