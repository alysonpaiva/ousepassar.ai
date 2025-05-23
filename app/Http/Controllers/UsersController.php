<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UsersController extends Controller
{
    //FUNÇÃO PARA EXIBIR A VIEW INDEX
    public function index(Request $request)
    {
        //OBTEM OS PEDIDOS
        $data['users'] = User::with('roleInfo')->orderByDesc('id');

        if ($request->has('name')) {
            $data['users']->where('name', 'like', "%" . $request->name . "%");
        }

        $countsByRole = \App\Models\User::with('roleInfo')
            ->get()
            ->groupBy('roleInfo.name')
            ->map(function ($group) {
                return $group->count();
            });

        $data['users_all'] = $data['users']->count();

        return view('users.index', [
            'users' => $data['users']->paginate(10)->appends($request->all()),
            'users_all' => $data['users_all'],
            'countsByRole' => $countsByRole
        ]);
    }

    //FUNÇÃO PARA EXIBIR A VIEW CREATE
    public function create()
    {
        //RETORNA A VIEW
        $roles = \App\Models\Role::all();
        return view('users.create')->with('roles', $roles);
    }

    //FUNÇÃO PARA CADASTRAR
    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'integer'],
        ]);

        //INICIA A TRANSAÇÃO
        DB::beginTransaction();

        //OBTEM OS DADOS DO REQUEST E FAZ O CADASTRO
        $data = $request->except('_token');
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        //ENVIA A TRASAÇÃO (COMMIT)
        DB::commit();

        //ENVIA MENSAGEM DE SUCESSO
        session()->flash('success', 'success');

        //RETORNA A VIEW
        return to_route('users.index');
    }

    //FUNÇÃO PARA EXIBIR A VIEW EDIT
    public function edit(User $user)
    {
        //RETORNA A VIEW
        $roles = \App\Models\Role::all();
        return view('users.edit')->with('user', $user)->with('roles', $roles);;
    }

    //FUNÇÃO PARA UPDATE
    public function update(Request $request, User $user)
    {
        //VALIDAÇÃO DA SENHA
        if ($request->password) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        //VALIDAÇÃO EMAIL
        if ($request->email != $user->email) {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            ]);
        }

        //VALIDAÇÕES
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $request->validate([
            'role' => ['required', 'integer'],
        ]);


        //INICIA A TRANSAÇÃO
        DB::beginTransaction();

        //VALIDAÇÃO E-MAIL
        if ($request->email != $user->email) {
            $user->email_verified_at = null;
        }

        $data = $request->except('_token');
        //VALIDAÇÃO PARA REDEFINIR SENHA
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            $data['password'] = $user->password;
        }

        $user->fill($data);
        $user->save();

        //ENVIA A TRASAÇÃO (COMMIT)
        DB::commit();

        //ENVIA MENSAGEM DE SUCESSO
        session()->flash('success', 'success');

        //RETORNA A VIEW
        return to_route('users.index');
    }

    //FUNÇÃO PARA EXIBIR A VIEW EDIT
    public function password(User $user)
    {
        //RETORNA A VIEW
        $roles = \App\Models\Role::all();
        return view('users.updatePassword')->with('user', $user)->with('roles', $roles);;
    }

    //FUNÇÃO PARA UPDATE PASSWORD
    public function updatePassword(Request $request, User $user)
    {
        //VALIDAÇÃO DA SENHA
        if ($request->password) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        }

        //INICIA A TRANSAÇÃO
        DB::beginTransaction();

        $data = $request->except('_token');
        //VALIDAÇÃO PARA REDEFINIR SENHA
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            $data['password'] = $user->password;
        }

        $user->fill($data);
        $user->save();

        //ENVIA A TRASAÇÃO (COMMIT)
        DB::commit();

        //ENVIA MENSAGEM DE SUCESSO
        session()->flash('success', 'success');

        //RETORNA A VIEW
        return to_route('users.index');
    }

    //FUNÇÃO PARA UPDATE
    public function destroy(User $user)
    {
        //INICIA A TRANSAÇÃO
        DB::beginTransaction();

        $user->delete();

        //ENVIA A TRASAÇÃO (COMMIT)
        DB::commit();

        //ENVIA MENSAGEM DE SUCESSO
        session()->flash('success', 'success');

        //RETORNA A VIEW
        return to_route('users.index');
    }
}
