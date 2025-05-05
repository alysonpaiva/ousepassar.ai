<x-app>
    @slot('stylesheet')
    @endslot

    @section('title', 'Dashboard')

    @slot('slot')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">

                <div class="card mb-5 mb-xl-10">
                    <div class="card-body pt-9 pb-0">
                        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-600">Seja bem-vindo(a)</span>
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="{{ route('profile.edit') }}"
                                                class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ Auth::user()->name }}</a>

                                            <span class="btn btn-sm btn-light-success fw-bold ms-2 fs-8 py-1 px-3">
                                                @if (Auth::user()->role === 1)
                                                    Administrador
                                                @else
                                                    Aluno
                                                @endif
                                            </span>
                                        </div>

                                    </div>
                                </div>

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
