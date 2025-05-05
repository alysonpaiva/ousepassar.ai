<x-guest>

    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

        <form method="POST" class="form w-100" id="kt_sign_in_form" action="{{ route('password.email') }}">
            @csrf
            <div class="text-center mb-11">
                <h1 class="text-dark fw-bolder mb-3">Esqueceu a senha?</h1>
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
                </div>
            @endif

            <div class="fv-row mb-10 fv-plugins-icon-container">
                <label class="form-label fs-6 fw-bold text-dark">E-mail</label>
                <input type="text" placeholder="E-mail" name="email" class="form-control bg-transparent" />
            </div>

            <div class="fv-row fv-plugins-icon-container mb-5">
                <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-warning w-100">
                    <span class="indicator-label text-black">Enviar</span>
                </button>
            </div>

            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Voltar para o login!') }}
            </a>

        </form>

    </div>

</x-guest>
