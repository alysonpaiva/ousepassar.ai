<x-guest>
    <div class="card rounded-3 w-md-550px">
        <div class="card-body p-10 p-lg-20">
            <form method="POST" class="form w-100" id="kt_sign_in_form" action="{{ route('password.store') }}">
                @csrf
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="text-center mb-11">
                    <h1 class="text-dark fw-bolder mb-3">Redefinição de senha</h1>
                </div>
                @if (Session::has('message'))
                    <div class="alert alert-danger">{{ Session::get('message') }}</div>
                @endif
                @if (Session::has('status'))
                    <div class="alert alert-danger">{{ Session::get('status') }}</div>
                @endif
                @if ($errors->get('email') || $errors->get('password') || $errors->get('password_confirmation'))
                    <div class="alert alert-danger">
                        @foreach ((array) $errors->get('email') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                        @foreach ((array) $errors->get('password') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                        @foreach ((array) $errors->get('password_confirmation') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </div>
                @endif
                <div class="fv-row mb-8">
                    <input type="text" placeholder="E-mail" name="email"
                        value="{{ old('email', $request->email) }}" class="form-control bg-transparent" />

                </div>
                <div class="fv-row mb-3">
                    <input type="password" placeholder="Senha" name="password" autocomplete="off"
                        class="form-control bg-transparent" />

                </div>
                <div class="fv-row mb-3">
                    <input type="password" placeholder="Confirmar Senha" name="password_confirmation" autocomplete="off"
                        class="form-control bg-transparent" />
                </div>
                <div class="d-grid mb-10">
                    <button type="submit" id="kt_sign_in_submit" class="btn btn-dark">
                        <span class="indicator-label">Enviar</span>
                    </button>
                </div>

                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('login') }}">
                    {{ __('Voltar para o login!') }}
                </a>
            </form>
        </div>
    </div>
</x-guest>
