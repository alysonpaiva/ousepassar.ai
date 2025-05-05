<x-app>
    @slot('stylesheet')
        <!-- STYLES -->
    @endslot

    @section('title', 'Meu Perfil')

    @slot('slot')

        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="row gy-5 g-xl-8">

                    <div class="col-xl-6">
                        <div class="card card-xl-stretch mb-3">
                            <div class="card-header border-0 py-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Informação do Perfil</span>
                                    <span class="text-muted mt-1 fw-semibold fs-7">Atualize as informações do perfil da sua
                                        conta e o endereço de e-mail.</span>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                                    @csrf
                                </form>

                                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('patch')

                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-bold mb-3" for="name">Nome</label>
                                        <input id="name" name="name" type="text"
                                            class="form-control form-control-lg form-control-solid"
                                            value="{{ old('name', $user->name) }}" required autocomplete="name">
                                        <span class="form-bar">
                                            @if ($errors->get('name'))
                                                <ul class="text-danger">
                                                    @foreach ((array) $errors->get('name') as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-bold mb-3" for="email">Email</label>
                                        <input id="email" name="email" type="email"
                                            class="form-control form-control-lg form-control-solid"
                                            value="{{ old('email', $user->email) }}" required autocomplete="email">
                                        <span class="form-bar">
                                            @if ($errors->get('email'))
                                                <ul class="text-danger">
                                                    @foreach ((array) $errors->get('email') as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <button type="submit" class="btn btn-light-success">Salvar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card card-xl-stretch mb-3">
                            <div class="card-header border-0 py-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Atualizar a senha</span>
                                    <span class="text-muted mt-1 fw-semibold fs-7">Certifique-se de que sua conta esteja
                                        usando uma senha longa e aleatória para permanecer
                                        segura.</span>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('put')
                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-bold mb-3" for="current_password">Senha
                                            Atual</label>
                                        <input id="current_password" name="current_password" type="password"
                                            class="form-control form-control-lg form-control-solid" required>
                                        <span class="form-bar">
                                            @if ($errors->updatePassword->get('current_password'))
                                                <ul class="text-danger">
                                                    @foreach ((array) $errors->updatePassword->get('current_password') as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-bold mb-3" for="password">Nova Senha</label>
                                        <input id="password" name="password" type="password"
                                            class="form-control form-control-lg form-control-solid" required>
                                        <span class="form-bar">
                                            @if ($errors->updatePassword->get('password'))
                                                <ul class="text-danger">
                                                    @foreach ((array) $errors->updatePassword->get('password') as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-bold mb-3" for="password_confirmation">Confirmação
                                            de
                                            senha</label>
                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                            class="form-control form-control-lg form-control-solid" required>
                                        <span class="form-bar">
                                            @if ($errors->updatePassword->get('password_confirmation'))
                                                <ul class="text-danger">
                                                    @foreach ((array) $errors->updatePassword->get('password_confirmation') as $message)
                                                        <li>{{ $message }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <button type="submit" class="btn btn-light-success">Salvar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- @if (Auth::user()->email_verified_at == null)
                <div class="mb-5 col-12">
                    <div class="p-4 card ">
                        <section>
                            <header>
                                <h5>
                                    Verificação de e-mail
                                </h5>

                                <div class="alert alert-danger col-xl-6 col-12">
                                    Clique no botão abaixo para enviar o email de verificação.
                                </div>
                            </header>

                            <div class="mt-4 flex items-center justify-between">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf

                                    <div>
                                        <button style="text-transform: none" type="submit"
                                            class="btn btn-primary b-radius-5">Enviar e-mail de verificação
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            @endif --}}

    @endslot

    @slot('scripts')
        <!-- SCRIPTS -->
    @endslot

</x-app>
