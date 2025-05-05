<x-app>

    @section('title', 'Histórico')

    @slot('slot')
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <div class="card mb-2">
                        <div class="card-header">
                            <h5 class="mb-0 card-title ">Detalhes da Interação</h5>

                            <div class="card-toolbar">
                                <a href="{{ route('pdf.history', $history->id) }}"
                                    class="btn btn-sm btn-outline-secondary me-2" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Gerar PDF
                                </a>
                                <button id="copyButton" class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="fas fa-copy"></i> Copiar texto
                                </button>

                                <a href="{{ route('agents.show', $agent) }}" class="btn btn-sm btn-dark">Voltar ao
                                    Agente</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Data da Interação</h6>
                                    <p>{{ $history->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Agente</h6>
                                    <p>{{ $agent->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 card-title ">Resultado</h5>
                        </div>
                        <div class="card-body">
                            <div id="resultText" class="mb-0">
                                {!! $history->result !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endslot

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const copyButton = document.getElementById('copyButton');
                const copyToast = document.getElementById('copyToast');
                const resultText = document.getElementById('resultText');

                // Função para copiar texto
                copyButton.addEventListener('click', function() {
                    const text = resultText.textContent;
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
                        alert('Não foi possível copiar o texto. Por favor, tente novamente.');
                    });
                });
            });
        </script>

    @endsection

</x-app>
