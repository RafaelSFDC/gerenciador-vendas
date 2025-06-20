<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário vendedor
        $vendedor = User::firstOrCreate([
            'email' => 'vendedor@dctecnologia.com'
        ], [
            'name' => 'Vendedor DC Tecnologia',
            'password' => Hash::make('123456'),
        ]);

        // Criar clientes
        $clientes = [
            [
                'nome' => 'João Silva',
                'email' => 'joao@email.com',
                'telefone' => '(11) 99999-9999',
                'cpf_cnpj' => '123.456.789-00',
                'endereco' => 'Rua das Flores, 123 - São Paulo/SP'
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria@email.com',
                'telefone' => '(11) 88888-8888',
                'cpf_cnpj' => '987.654.321-00',
                'endereco' => 'Av. Paulista, 456 - São Paulo/SP'
            ],
            [
                'nome' => 'Empresa ABC Ltda',
                'email' => 'contato@empresaabc.com',
                'telefone' => '(11) 77777-7777',
                'cpf_cnpj' => '12.345.678/0001-90',
                'endereco' => 'Rua Comercial, 789 - São Paulo/SP'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::firstOrCreate(['email' => $cliente['email']], $cliente);
        }

        // Criar produtos
        $produtos = [
            [
                'nome' => 'Notebook Dell Inspiron',
                'descricao' => 'Notebook Dell Inspiron 15 3000, Intel Core i5, 8GB RAM, 256GB SSD',
                'preco' => 2499.99,
                'estoque' => 10,
                'ativo' => true
            ],
            [
                'nome' => 'Mouse Logitech MX Master',
                'descricao' => 'Mouse sem fio Logitech MX Master 3, sensor de alta precisão',
                'preco' => 299.99,
                'estoque' => 25,
                'ativo' => true
            ],
            [
                'nome' => 'Teclado Mecânico Corsair',
                'descricao' => 'Teclado mecânico Corsair K70, switches Cherry MX Red, RGB',
                'preco' => 599.99,
                'estoque' => 15,
                'ativo' => true
            ],
            [
                'nome' => 'Monitor LG UltraWide',
                'descricao' => 'Monitor LG UltraWide 29", resolução 2560x1080, IPS',
                'preco' => 1299.99,
                'estoque' => 8,
                'ativo' => true
            ],
            [
                'nome' => 'Headset HyperX Cloud',
                'descricao' => 'Headset gamer HyperX Cloud II, som surround 7.1',
                'preco' => 399.99,
                'estoque' => 20,
                'ativo' => true
            ]
        ];

        foreach ($produtos as $produto) {
            Produto::firstOrCreate(['nome' => $produto['nome']], $produto);
        }

        // Criar formas de pagamento
        $formasPagamento = [
            [
                'nome' => 'Dinheiro',
                'descricao' => 'Pagamento à vista em dinheiro',
                'ativo' => true
            ],
            [
                'nome' => 'Cartão de Crédito',
                'descricao' => 'Pagamento com cartão de crédito (parcelado)',
                'ativo' => true
            ],
            [
                'nome' => 'Cartão de Débito',
                'descricao' => 'Pagamento à vista com cartão de débito',
                'ativo' => true
            ],
            [
                'nome' => 'PIX',
                'descricao' => 'Pagamento instantâneo via PIX',
                'ativo' => true
            ],
            [
                'nome' => 'Boleto Bancário',
                'descricao' => 'Pagamento via boleto bancário',
                'ativo' => true
            ]
        ];

        foreach ($formasPagamento as $forma) {
            FormaPagamento::firstOrCreate(['nome' => $forma['nome']], $forma);
        }
    }
}
