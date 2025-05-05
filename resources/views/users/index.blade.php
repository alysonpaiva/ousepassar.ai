<x-app>
    @slot('stylesheet')
    @endslot

    @section('title', 'Usuários')

    @slot('slot')

        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="container-xxl" id="kt_content_container">
                <div class="row gy-5 g-xl-8">

                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mb-md-5 mb-xl-10">
                        <div class="card card-flush mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <div class="card-title m-0">
                                        <div class="symbol symbol-45px w-45px bg-light me-5 d-flex justify-content-center">
                                            <i style="font-size: 25px" class="fa fa-users p-3"></i>
                                        </div>
                                        <span class="fs-4 fw-semibold text-gray-600 m-0">
                                            Administradores
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                <div class="fs-2tx fw-bold mb-3">
                                    {{ $users->where('roleInfo.name', 'Administrador')->count() }}
                                </div>
                                <div class="d-flex align-items-center flex-wrap mb-5 mt-auto fs-6">
                                    <i class="ki-duotone ki-Up-right fs-3 me-1 text-danger"></i>
                                    <div class="fw-semibold text-gray-400">
                                        Total de administradores
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-4 col-xl-4 col-xxl-4 mb-md-5 mb-xl-10">
                        <div class="card card-flush mb-5 mb-xl-10">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <div class="card-title m-0">
                                        <div class="symbol symbol-45px w-45px bg-light me-5 d-flex justify-content-center">
                                            <i style="font-size: 25px" class="fa fa-users p-3"></i>
                                        </div>
                                        <span class="fs-4 fw-semibold text-gray-600 m-0">
                                            Alunos
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-end pe-0">
                                <div class="fs-2tx fw-bold mb-3">
                                    {{ $users->where('roleInfo.name', 'Aluno')->count() }}
                                </div>
                                <div class="d-flex align-items-center flex-wrap mb-5 mt-auto fs-6">
                                    <i class="ki-duotone ki-Up-right fs-3 me-1 text-danger"></i>
                                    <div class="fw-semibold text-gray-400">
                                        Total de Alunos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-md-5 mb-xl-10">
                        <div class="card card-flush mb-5 mb-xl-10">
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                </div>
                                <div class="card-toolbar">
                                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                        <button type="button" class="btn btn-light-dark me-3" data-kt-menu-trigger="click"
                                            data-kt-menu-placement="bottom-end">
                                            Filtros
                                        </button>
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                            <div class="px-7 py-5">
                                                <div class="fs-5 text-dark fw-bold">Opções de Filtrod</div>
                                            </div>
                                            <div class="separator border-gray-200"></div>
                                            <form action="{{ route('users.index') }}" method="GET" class="px-7 py-5"
                                                data-kt-user-table-filter="form">
                                                <div class="mb-10">
                                                    <label class="form-label fs-6 fw-semibold">Nome:</label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        placeholder="Nome"
                                                        value="{{ !empty($_GET['name']) ? $_GET['name'] : '' }}">
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <a href="{{ route('users.index') }}">
                                                        <button type="button"
                                                            class="btn btn-light btn-active-light-dark fw-semibold me-2 px-6"
                                                            data-kt-menu-dismiss="true"
                                                            data-kt-user-table-filter="reset">Limpar
                                                        </button>
                                                    </a>

                                                    <button type="submit" class="btn btn-dark fw-semibold px-6"
                                                        data-kt-menu-dismiss="true"
                                                        data-kt-user-table-filter="filter">Aplicar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center d-none"
                                        data-kt-user-table-toolbar="selected">
                                        <div class="fw-bold me-5">
                                            <span class="me-2" data-kt-user-table-select="selected_count"></span>
                                            Selecionado
                                        </div>
                                        <button type="button" class="btn btn-danger"
                                            data-kt-user-table-select="delete_selected">
                                            Deleter Selecionado
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-4">
                                <div id="kt_ecommerce_products_table_wrapper"
                                    class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                    <th class="w-1px pe-0"></th>
                                                    <th class="w-10px pe-2">
                                                        #ID
                                                    </th>
                                                    <th class="min-w-125px">Nome</th>
                                                    <th class="min-w-125px">E-mail</th>
                                                    <th class="min-w-125px">Função</th>
                                                    <th class="min-w-125px">Data Cadastro</th>
                                                    <th class="w-1px"></th>
                                                    <th class="text-center min-w-50px">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-semibold">
                                                <?php foreach ($users as $user) { ?>
                                                <tr>
                                                    <td></td>
                                                    <td>#{{ $user->id }}</td>
                                                    <td class="d-flex align-items-center">
                                                        <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                            <a href="{{ route('users.edit', $user->id) }}">
                                                                <div class="symbol-label">
                                                                    <i class="fa fa-user"></i>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ route('users.edit', $user->id) }}"
                                                                class="text-gray-800 text-hover-dark mb-1">{{ $user->name }}</a>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->roleInfo->name ?? '-' }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                                                    <td>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="#"
                                                            class="btn btn-light btn-active-light-dark btn-flex btn-center btn-sm"
                                                            data-kt-menu-trigger="click"
                                                            data-kt-menu-placement="bottom-end">
                                                            Ações
                                                            <i class="fa fa-chevron-down fs-6 ms-2"></i></a>
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-dark fw-semibold fs-7 w-125px py-4"
                                                            data-kt-menu="true">
                                                            <div class="menu-item px-3">
                                                                <a href="{{ route('users.edit', $user->id) }}"
                                                                    class="menu-link px-3">
                                                                    Editar
                                                                </a>
                                                            </div>
                                                            <div class="menu-item px-3">
                                                                <a href="{{ route('users.password', $user->id) }}"
                                                                    class="menu-link px-3">
                                                                    Alterar senha
                                                                </a>
                                                            </div>
                                                            <div class="menu-item px-3">
                                                                <a class="menu-link px-3">
                                                                    <form id="form-delete"
                                                                        action="{{ route('users.destroy', $user->id) }}"
                                                                        method="post">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">
                                                                            Excluir
                                                                        </button>
                                                                    </form>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="pr-4 pl-4">
                                    @if ($users->hasPages())
                                        <div class="d-flex justify-content-between">
                                            <div id="mostrarPaginas">
                                                <p class="text-sm text-gray leading-5">
                                                    Mostrando
                                                    <span class="font-medium">{{ $users->firstItem() }}</span>
                                                    até
                                                    <span class="font-medium">{{ $users->lastItem() }}</span>
                                                    de
                                                    <span class="font-medium">{{ $users->total() }}</span>
                                                    resultados
                                                </p>
                                            </div>
                                            {{ $users->links() }}
                                        </div>
                                    @endif
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
