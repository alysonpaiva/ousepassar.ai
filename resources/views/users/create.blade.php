<x-app>
    @slot('stylesheet')
        <!-- STYLES -->
    @endslot

    @section('title', 'Cadastrar Usuário')

    @slot('slot')

        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">

                <div class="card card-flush mb-5 mb-xl-10">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">
                            <div class="row">

                                <div class="col-xl-6 col-md-12 col-12 mb-2">

                                    <div class="form-group ">
                                        <label for="name" class="form-label fs-6 fw-bold mb-3 required">Nome</label>
                                        <input type="text" class="form-control mb-4" id="name" name="name"
                                            placeholder="Nome" value="{{ old('name') }}">
                                    </div>
                                    @if ($errors->get('name'))
                                        @foreach ((array) $errors->get('name') as $message)
                                            <li class="text-danger">{{ $message }}</li>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-xl-6 col-md-6 col-12 mb-2">

                                    <div class="form-group">
                                        <label for="email" class="form-label fs-6 fw-bold mb-3 required">E-mail</label>
                                        <input type="email" class="form-control mb-4" id="email" name="email"
                                            placeholder="E-mail" value="{{ old('email') }}">
                                    </div>
                                    @if ($errors->get('email'))
                                        @foreach ((array) $errors->get('email') as $message)
                                            <li class="text-danger">{{ $message }}</li>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-xl-4 col-md-6 col-12 mb-2">

                                    <div class="form-group">
                                        <label for="role" class="form-label fs-6 fw-bold mb-3 required">Função</label>

                                        <select name="role" id="role" class="form-control">
                                            @foreach ($roles as $role)
                                                <option value="<?= $role->id ?>"><?= $role->name ?>
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                    @if ($errors->get('role'))
                                        @foreach ((array) $errors->get('role') as $message)
                                            <li class="text-danger">{{ $message }}</li>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-xl-4 col-md-6 col-12 mb-2">

                                    <div class="form-group">
                                        <label for="password" class="form-label fs-6 fw-bold mb-3 required">Senha</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Senha" value="">
                                    </div>
                                    @if ($errors->get('password'))
                                        @foreach ((array) $errors->get('password') as $message)
                                            <li class="text-danger">{{ $message }}</li>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-xl-4 col-md-6 col-12 mb-2">

                                    <div class="form-group">
                                        <label for="password_confirmation"
                                            class="form-label fs-6 fw-bold mb-3 required">Comfirme a
                                            senha</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Senha" value="">
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
                            <button type="submit" class="btn btn-success">Cadastrar</button>
                        </div>
                    </form>
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
