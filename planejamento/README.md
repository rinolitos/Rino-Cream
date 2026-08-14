# Planejamento - Rino Cream

## Tema

O projeto Rino Cream tem como tema uma sorveteria com sistema de pedidos online.

## Proposta

Criar um site no qual o cliente possa visualizar os produtos disponíveis, adicionar itens ao carrinho, realizar pedidos e acompanhar o andamento da compra.

O sistema também possui uma área administrativa para gerenciamento dos produtos, estoque e pedidos.

## Público-alvo

O público-alvo são clientes de uma sorveteria que desejam visualizar o cardápio e realizar pedidos de forma simples e rápida.

## Identidade visual

A identidade visual foi desenvolvida utilizando cores suaves relacionadas a sorvetes e sobremesas.

### Paleta de cores

```text
Azul claro: #6EC6E8
Rosa:       #F58FA3
Baunilha:   #FFF7E8
Marrom:     #5A3E36
Azul:       #3185A8
Branco:     #FFFFFF
```

### Fontes

```text
Fredoka
Nunito
```

## Estrutura inicial do site

```text
Página inicial
│
├── Cardápio
│   ├── Sorvetes
│   ├── Milk-shakes
│   ├── Açaí
│   └── Sobremesas
│
├── Carrinho
│
├── Conta
│   ├── Login
│   └── Cadastro
│
├── Meus pedidos
│
└── Contato
```

## Área administrativa

```text
Administrador
│
├── Painel
│
├── Produtos
│   ├── Cadastrar
│   ├── Editar
│   └── Ativar / Desativar
│
└── Pedidos
    ├── Visualizar
    ├── Alterar status
    └── Cancelar
```

## Fluxo do cliente

```text
Acessar o site
      ↓
Ver o cardápio
      ↓
Adicionar produtos
      ↓
Abrir o carrinho
      ↓
Entrar ou criar conta
      ↓
Finalizar pedido
      ↓
Acompanhar pedido
```

## Fluxo do pedido

```text
Aguardando
    ↓
Preparando
    ↓
Pronto
    ↓
Entregue
```

Também é possível cancelar um pedido antes da entrega:

```text
Aguardando ──→ Cancelado
Preparando ──→ Cancelado
Pronto ──────→ Cancelado
```

## Banco de dados

O sistema utiliza as seguintes tabelas:

```text
usuarios
clientes
categorias
produtos
pedidos
itens_pedido
```

As tabelas são relacionadas para permitir o cadastro de clientes, produtos e pedidos.

## Responsividade

O projeto foi planejado para funcionar em computadores e dispositivos móveis.

Em telas menores, o menu de navegação é adaptado para um menu mobile.

## Funcionalidades planejadas

- Cadastro e login de clientes
- Cardápio de produtos
- Categorias
- Carrinho de compras
- Controle de estoque
- Produtos esgotados
- Finalização de pedidos
- Histórico de pedidos
- Área administrativa
- Cadastro e edição de produtos
- Controle de pedidos
- Atualização de status
- Cancelamento com devolução de estoque
- Layout responsivo