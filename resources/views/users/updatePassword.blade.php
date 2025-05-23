<x-app>
    @slot('stylesheet')
        <!-- STYLES -->
    @endslot

    @slot('slot')
        <div class="row g-5 g-xl-8">
            <div class="col-xl-12">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Editar usuário</h1>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="row">
                    <div class="col">
                        <div class="card card-primary">
                            <form action="{{ route('users.updatePassword', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>Insira as informações</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row">

                                                    <div class="col-xl-6 col-md-6 col-12 mb-2">

                                                        <div class="form-group">
                                                            <label for="password"
                                                                class="form-label fs-6 fw-bold mb-3 required">Senha</label>
                                                            <input type="password" class="form-control" id="password"
                                                                name="password" placeholder="Senha" value="">
                                                        </div>
                                                        @if ($errors->get('password'))
                                                            @foreach ((array) $errors->get('password') as $message)
                                                                <li class="text-danger">{{ $message }}</li>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                    <div class="col-xl-6 col-md-6 col-12 mb-2">

                                                        <div class="form-group">
                                                            <label for="password_confirmation"
                                                                class="form-label fs-6 fw-bold mb-3 required">Comfirme a
                                                                senha</label>
                                                            <input type="password" class="form-control"
                                                                id="password_confirmation" name="password_confirmation"
                                                                placeholder="Senha" value="">
                                                        </div>
                                                        @if ($errors->get('password_confirmation'))
                                                            @foreach ((array) $errors->get('password_confirmation') as $message)
                                                                <li class="text-danger">{{ $message }}</li>
                                                            @endforeach
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endslot

    @slot('scripts')
        <script>
            $('#funcao').on('change', function() {
                $('#funcao').val() == '2' ? $('#sweepstakes').show() : $('#sweepstakes').hide()
            })

            if ($('#funcao').val() == 2) {
                $('#sweepstakes').show()
            }
        </script>
    @endslot
</x-app>
