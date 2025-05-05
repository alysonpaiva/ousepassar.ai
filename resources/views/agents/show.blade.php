<x-app>
    @slot('stylesheet')
        <link href="{{ asset('/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    @endslot

    @section('title', 'Agente')

    @slot('slot')

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

                        <div class="card card-flush">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    Insira as Informações
                                </h3>
                            </div>

                            <div class="card-body">
                                <form id="agentForm" action="{{ route('agents.process', $agent) }}" method="POST"
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
                                            @elseif($field->type === 'date')
                                                <input type="date" class="form-control" id="field_{{ $field->id }}"
                                                    name="field_{{ $field->id }}"
                                                    {{ $field->required ? 'required' : '\' ?>' }}>
                                            @elseif($field->type === 'upload')
                                                <input type="file" class="form-control" id="field_{{ $field->id }}"
                                                    name="field_{{ $field->id }}"
                                                    {{ $field->required ? 'required' : '' }}>
                                            @endif
                                        </div>
                                    @endforeach

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-warning text-black">
                                            <i class="fas fa-play text-black"></i> Processar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">

                        <div class="card h-md-100">
                            <div class="card-header border-bottom-1">

                                <ul class="nav nav-stretch nav-pills nav-pills-custom d-flex mt-4" role="tablist">
                                    <li class="nav-item p-0 ms-0" role="presentation">
                                        <a class="nav-link btn btn-color-gray-400 flex-center px-3 active"
                                            data-kt-timeline-widget-4="tab" data-bs-toggle="tab" href="#output"
                                            aria-selected="true" role="tab">
                                            <span class="nav-text fw-semibold fs-4 mb-3">Output</span>

                                            <span
                                                class="bullet-custom position-absolute z-index-2 w-100 h-1px top-100 bottom-n100 bg-primary rounded"></span>
                                        </a>
                                    </li>
                                    <li class="nav-item p-0 ms-0" role="presentation">
                                        <a class="nav-link btn btn-color-gray-400 flex-center px-3"
                                            data-kt-timeline-widget-4="tab" data-bs-toggle="tab" href="#historico"
                                            aria-selected="true" role="tab">
                                            <span class="nav-text fw-semibold fs-4 mb-3">Histórico</span>

                                            <span
                                                class="bullet-custom position-absolute z-index-2 w-100 h-1px top-100 bottom-n100 bg-primary rounded"></span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="btn-group">
                                    <button id="copyButton" class="btn btn-sm" title="Copiar texto">
                                        <i class="fas fa-copy"></i> Copiar texto
                                    </button>
                                    <button id="pdfButton" class="btn btn-sm" title="Gerar PDF">
                                        <i class="fas fa-file-pdf"></i> Gerar PDF
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div id="loadingIndicator" class="text-center my-4" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="mt-2">Pensando...</p>
                                </div>

                                <div class="tab-content" id="myTabContent">

                                    <div class="tab-pane fade show active" id="output" role="tabpanel">

                                        <div id="resultArea" class="mt-4" style="">
                                            <div id="resultContent" class="markdown">
                                                <p class="text-center text-muted mt-5">Preencha as informações e clique em
                                                    "Processar" para ver o resultado aqui.</p>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="historico" role="tabpanel">

                                        <div class="table-responsive">
                                            <table id="table-historico" class="table table-row-bordered gy-5">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Prévia</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($recentHistory as $item)
                                                        <tr class="fw-semibold fs-6 text-gray-800">
                                                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <div class="" style="max-width: 300px;">
                                                                    {!! Str::limit($item->result, 100) !!}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('agents.history.detail', $item) }}"
                                                                    class="btn btn-sm btn-dark">
                                                                    Ver Detalhes
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Visualizar Imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="Imagem em tamanho completo" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário oculto para geração de PDF -->
        <form id="pdfForm" action="{{ route('pdf.generate') }}" method="POST" target="_blank"
            style="display: none;">
            @csrf
            <input type="hidden" name="agent_id" id="pdfAgentId" value="{{ $agent->id }}">
            <input type="hidden" name="result" id="pdfResult">
            <input type="hidden" name="user_inputs" id="pdfUserInputs">
            <input type="hidden" name="prompt" id="pdfPrompt">
        </form>

    @endslot

    @section('scripts')

        <script src="{{ asset('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
            //table 
            $("#table-historico").DataTable({
                "scrollX": true
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('agentForm');
                const loadingIndicator = document.getElementById('loadingIndicator');
                const userInputsArea = document.getElementById('userInputsArea');
                const userInputsContent = document.getElementById('userInputsContent');
                const resultArea = document.getElementById('resultArea');
                const resultContent = document.getElementById('resultContent');
                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                const modalImage = document.getElementById('modalImage');
                const modalTitle = document.getElementById('imageModalLabel');

                const copyButton = document.getElementById('copyButton');
                const pdfButton = document.getElementById('pdfButton');
                const copyToast = document.getElementById('copyToast');
                const pdfForm = document.getElementById('pdfForm');
                const pdfResult = document.getElementById('pdfResult');
                const pdfUserInputs = document.getElementById('pdfUserInputs');
                const pdfPrompt = document.getElementById('pdfPrompt');

                // Inicializar toast
                const toast = new bootstrap.Toast(copyToast);

                // Função para copiar texto
                copyButton.addEventListener('click', function() {
                    const text = resultContent.textContent;
                    navigator.clipboard.writeText(text).then(function() {

                        // Mostrar notificação de sucesso
                        Swal.fire({
                            text: "Texto copiado!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: 'Certo',
                            customClass: {
                                confirmButton: "btn btn-success"
                            }
                        });

                    }).catch(function(err) {
                        console.error('Erro ao copiar texto: ', err);

                        Swal.fire({
                            text: "Não foi possível copiar o texto. Por favor, tente novamente.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: 'Certo',
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });

                    });
                });

                // Função para gerar PDF
                pdfButton.addEventListener('click', function() {
                    // Preencher o formulário com os dados atuais
                    pdfResult.value = marked.parse(resultContent.innerHTML);
                    pdfUserInputs.value = JSON.stringify(window.currentUserInputs || {});
                    pdfPrompt.value = window.currentPrompt || '';

                    // Enviar o formulário
                    pdfForm.submit();
                });

                // Função para abrir o modal com a imagem
                window.openImageModal = function(url, title) {
                    modalImage.src = url;
                    modalTitle.textContent = title || 'Visualizar Imagem';
                    imageModal.show();
                };

                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Mostrar indicador de carregamento
                    loadingIndicator.style.display = 'block';
                    // userInputsArea.style.display = 'none';
                    resultArea.style.display = 'none';

                    // Criar FormData para enviar o formulário com arquivos
                    const formData = new FormData(form);

                    // Fazer a requisição AJAX
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erro na requisição: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Esconder indicador de carregamento
                            loadingIndicator.style.display = 'none';

                            // Exibir as informações enviadas pelo usuário
                            // renderUserInputs(data.userInputs);
                            // userInputsArea.style.display = 'block';

                            // Exibir resultado
                            resultContent.innerHTML = marked.parse(data.result);
                            resultArea.style.display = 'block';

                            // Rolar até as informações enviadas
                            resultContent.scrollIntoView({
                                behavior: 'smooth'
                            });
                        })
                        .catch(error => {
                            // Esconder indicador de carregamento
                            loadingIndicator.style.display = 'none';

                            // Exibir mensagem de erro
                            resultContent.innerHTML = 'Ocorreu um erro ao processar sua solicitação: ' +
                                error.message;
                            resultArea.style.display = 'block';

                            console.error('Erro:', error);
                        });
                });

                // Função para renderizar as informações enviadas pelo usuário
                function renderUserInputs(inputs) {
                    let html = '<div class="row">';

                    for (const [label, info] of Object.entries(inputs)) {

                        html += `<div class="col-md-6">`;

                        if (info.type === 'image') {
                            // Miniatura de imagem com link para abrir em tamanho maior

                            html +=
                                `<div class="border border-dashed border-gray-300 rounded px-5 py-3 mb-6">`;
                            html += `<div class="d-flex flex-stack">`;
                            html +=
                                `<img src="${info.url}" class="w-50px ms-n1 me-1 img-thumbnail" alt="${info.name}" onclick="openImageModal('${info.url}', '${info.name}')" style="max-height: 80px; cursor: pointer">`;
                            html += `${info.name}`;
                            html += `<span class="badge badge-light-success">${label}</span> `;
                            html += `</div>`;
                            html += `</div>`;
                        } else if (info.type === 'pdf') {
                            // Link para abrir PDF em nova aba
                            html +=
                                `<div class="border border-dashed border-gray-300 rounded px-5 py-3 mb-6">`;
                            html += `<div class="d-flex flex-stack">`;
                            html +=
                                `<a href="${info.url}" target="_blank" class="btn btn-sm btn-outline-primary">`;
                            html +=
                                `<i class="far fa-file-pdf fa-4x text-danger mb-2"></i>`;
                            html += `${info.name}`;
                            html += `</a>`;
                            html += `<span class="badge badge-light-success">${label}</span> `;
                            html += `</div>`;
                            html += `</div>`;
                        } else {
                            // Texto simples
                            html +=
                                `<div class="border border-dashed border-gray-300 rounded px-5 py-3 mb-6">`;
                            html += `<div class="d-flex flex-stack">`;
                            html += `${info.value || 'Vazio'}`;
                            html += `<span class="badge badge-light-success">${label}</span> `;
                            html += `</div>`;
                            html += `</div>`;
                        }

                        html += `</div>`;
                    }

                    html += '</div>';
                    userInputsContent.innerHTML = html;
                }
            });
        </script>
    @endsection

</x-app>
