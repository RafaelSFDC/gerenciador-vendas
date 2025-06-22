@extends('app')

@section('title', 'Editar Produto')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit me-2"></i>
            Editar Produto
        </h1>
        <div class="btn-group">
            <a href="{{ route('produtos.show', $produto) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>
                Visualizar
            </a>
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Dados do Produto
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('produtos.update', $produto) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">
                                    Nome do Produto <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome', $produto->nome) }}" 
                                       required
                                       placeholder="Nome do produto">
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="preco" class="form-label">
                                    Preço <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" 
                                           class="form-control @error('preco') is-invalid @enderror" 
                                           id="preco" 
                                           name="preco" 
                                           value="{{ old('preco', $produto->preco) }}" 
                                           step="0.01"
                                           min="0"
                                           required
                                           placeholder="0,00">
                                    @error('preco')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                      id="descricao" 
                                      name="descricao" 
                                      rows="3"
                                      placeholder="Descrição detalhada do produto">{{ old('descricao', $produto->descricao) }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estoque" class="form-label">
                                    Estoque <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control @error('estoque') is-invalid @enderror" 
                                       id="estoque" 
                                       name="estoque" 
                                       value="{{ old('estoque', $produto->estoque) }}" 
                                       min="0"
                                       required
                                       placeholder="Quantidade em estoque">
                                @error('estoque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="ativo" 
                                           name="ativo" 
                                           value="1"
                                           {{ old('ativo', $produto->ativo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Produto ativo
                                    </label>
                                </div>
                                <small class="text-muted">Produtos inativos não aparecem na lista de vendas</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('produtos.show', $produto) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Atualizar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Formatação do preço
document.getElementById('preco').addEventListener('input', function(e) {
    let value = e.target.value;
    // Permitir apenas números e ponto decimal
    value = value.replace(/[^0-9.]/g, '');
    
    // Garantir apenas um ponto decimal
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    
    e.target.value = value;
});
</script>
@endpush
