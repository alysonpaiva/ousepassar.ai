<x-app>
    @slot('stylesheet')
        <!-- STYLES -->
    @endslot

    @section('title', 'Editar Usuário')

    @slot('slot')

        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">

                <div class="card card-flush mb-5 mb-xl-10">

                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">

                                <div class="col-xl-4 col-6 mb-2">

                                    <div class="form-group ">
                                        <label for="name" class="form-label fs-6 fw-bold mb-3 required">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Nome" value="{{ old('name') ?? $user->name }}">
                                    </div>
                                    @if ($errors->get('name'))
                                        @foreach ((array) $errors->get('name') as $message)
                                            <li class="text-danger">{{ $message }}</li>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="col-xl-4 col-md-6 col-12 mb-2">

                                    <div class="form-group">
                                        <label for="email" class="form-label fs-6 fw-bold mb-3 required">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="E-mail" value="{{ old('email') ?? $user->email }}">
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
                                                <option <?= $user->role == $role->id ? 'selected' : '' ?>
                                                    value="<?= $role->id ?>"><?= $role->name ?>
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

                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Salvar</button>
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
