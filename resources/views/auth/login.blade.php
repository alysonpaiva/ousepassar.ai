<x-guest>

    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

        <form method="POST" class="form w-100" id="kt_sign_in_form" action="{{ route('login') }}">
            @csrf
            <div class="text-center mb-11">
                <h1 class="text-dark fw-bolder mb-3">Acessar Painel</h1>
            </div>
            @if (Session::has('message'))
                <div class="alert alert-danger">{{ Session::get('message') }}</div>
            @endif
            @if (Session::has('status'))
                <div class="alert alert-warning">{{ Session::get('status') }}</div>
            @endif
            @if ($errors->get('email') || $errors->get('password'))
                <div class="alert alert-danger">
                    @foreach ((array) $errors->get('email') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                    @foreach ((array) $errors->get('password') as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </div>
            @endif

            <div class="fv-row mb-10 fv-plugins-icon-container">
                <label class="form-label fs-6 fw-bold text-dark">E-mail</label>
                <input type="text" placeholder="E-mail" name="email" class="form-control bg-transparent" />
            </div>

            <div class="fv-row mb-10 fv-plugins-icon-container">
                <div class="d-flex flex-stack mb-2">
                    <label class="form-label fw-bold text-dark fs-6 mb-0">Senha</label>

                    <a href="{{ route('password.request') }}"
                        class="link-warning fs-6 fw-bold">
                        Esqueceu sua senha?
                    </a>
                </div>
                <input type="password" placeholder="Senha" name="password" autocomplete="off"
                    class="form-control bg-transparent" />
            </div>

            <div class="fv-row fv-plugins-icon-container">
                <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-warning w-100">
                    <span class="indicator-label text-black">Entrar</span>
                    <span class="indicator-progress">Aguarde...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button> 
            </div>

        </form>

    </div>

</x-guest>
