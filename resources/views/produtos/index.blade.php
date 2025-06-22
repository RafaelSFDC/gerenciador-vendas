@extends('app')

@section('title', 'Produtos')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box me-2"></i>
            Produtos
        </h1>
        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Novo Produto
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Produtos
            </h5>
        </div>
        <div class="card-body">
            @if($produtos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produtos as $produto)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $produto->nome }}</strong>
                                            @if($produto->descricao)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($produto->descricao, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        @if($produto->estoque > 0)
                                            <span class="badge bg-info">{{ $produto->estoque }} unidades</span>
                                        @else
                                            <span class="badge bg-warning">Sem estoque</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($produto->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('produtos.show', $produto) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('produtos.edit', $produto) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('produtos.destroy', $produto) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  id="form-excluir-{{ $produto->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Excluir"
                                                        onclick="confirmarExclusao({{ $produto->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $produtos->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum produto cadastrado</h5>
                    <p class="text-muted">Clique no botão "Novo Produto" para cadastrar o primeiro produto.</p>
                    <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Cadastrar Primeiro Produto
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(produtoId) {
    if (confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')) {
        document.getElementById('form-excluir-' + produtoId).submit();
    }
}
</script>
@endpush
