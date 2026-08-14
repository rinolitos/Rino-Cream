# 🍦 Rino Cream

O **Rino Cream** é um sistema web desenvolvido para uma sorveteria fictícia.

O projeto permite que clientes visualizem o cardápio, criem uma conta, adicionem produtos ao carrinho e realizem pedidos. Também possui uma área administrativa para gerenciamento de produtos, estoque e andamento dos pedidos.

---

## Sobre o projeto

A proposta do Rino Cream é simular o funcionamento de uma sorveteria com sistema de pedidos online.

O site foi desenvolvido utilizando PHP integrado com banco de dados MySQL, além de HTML, CSS e JavaScript para a interface e interações com o usuário.

O sistema possui dois tipos de usuários:

**Cliente:** pode criar conta, acessar o cardápio, utilizar o carrinho, realizar pedidos e acompanhar o histórico das compras.

**Administrador:** pode cadastrar e editar produtos, controlar estoque e acompanhar e atualizar o status dos pedidos.

---

## Linguagens utilizadas

- HTML
- CSS
- JavaScript
- PHP
- MySQL / MariaDB
- PDO
- XAMPP
- phpMyAdmin

---

## Funcionalidades

### Cliente

- Cadastro de conta
- Login e logout
- Visualização do cardápio
- Produtos separados por categorias
- Controle de estoque
- Identificação de produtos esgotados
- Carrinho de compras
- Alteração da quantidade de produtos
- Limite de produtos de acordo com o estoque
- Atualização do contador do carrinho sem recarregar a página
- Finalização de pedidos
- Histórico de pedidos
- Visualização dos detalhes de cada pedido

### Administrador

- Login administrativo
- Painel administrativo
- Cadastro de produtos
- Edição de produtos
- Ativação e desativação de produtos
- Controle de estoque
- Visualização dos pedidos realizados
- Visualização dos dados do cliente e itens do pedido
- Atualização do andamento dos pedidos
- Cancelamento de pedidos
- Devolução automática dos produtos ao estoque em caso de cancelamento

---

## Fluxo dos pedidos

Os pedidos seguem o seguinte fluxo:

```text
Aguardando
    ↓
Preparando
    ↓
Pronto
    ↓
Entregue
```

Enquanto o pedido ainda não foi entregue, ele também pode ser cancelado.

```text
Aguardando ──→ Cancelado
Preparando ──→ Cancelado
Pronto ──────→ Cancelado
```

Os status **Entregue** e **Cancelado** encerram o pedido e não podem ser alterados novamente.

Quando um pedido é cancelado, a quantidade dos produtos é devolvida automaticamente ao estoque.

---

## Estrutura do projeto

```text
RinoCream/
│
├── logo/
│   └── logo.png
│
├── planejamento/
│
├── site/
│   │
│   ├── admin/
│   │   ├── pedidos/
│   │   ├── produtos/
│   │   ├── criar_admin.php
│   │   └── index.php
│   │
│   ├── carrinho/
│   ├── config/
│   ├── conta/
│   ├── css/
│   ├── imagens/
│   ├── includes/
│   ├── js/
│   ├── pedidos/
│   │
│   ├── cardapio.php
│   ├── contato.php
│   └── index.php
│
├── README.md
└── rinocream.sql
```

---

## Banco de dados

O banco de dados utilizado pelo projeto se chama:

```text
rinocream
```

As principais tabelas são:

```text
usuarios
clientes
categorias
produtos
pedidos
itens_pedido
```

O arquivo para criação do banco está disponível na raiz do projeto:

```text
rinocream.sql
```

---

## Como executar o projeto

### 1. Instalar o XAMPP

É necessário possuir o XAMPP instalado no computador.

Inicie:

```text
Apache
MySQL
```

### 2. Copiar o projeto

Coloque a pasta `RinoCream` dentro de:

```text
C:\xampp\htdocs\
```

O caminho final deverá ser:

```text
C:\xampp\htdocs\RinoCream
```

### 3. Criar o banco de dados

Abra o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Utilize a opção **Importar** e selecione o arquivo:

```text
rinocream.sql
```

O banco `rinocream` e suas tabelas serão criados automaticamente.

### 4. Criar o primeiro administrador

Após importar o banco, abra:

```text
http://localhost/RinoCream/site/admin/criar_admin.php
```

Informe o e-mail e a senha que serão utilizados para acessar a área administrativa.

A página de criação fica bloqueada depois que o primeiro administrador é cadastrado.

### 5. Abrir o site

A página inicial pode ser acessada em:

```text
http://localhost/RinoCream/site/
```

A área administrativa pode ser acessada em:

```text
http://localhost/RinoCream/site/admin/
```

---

## Controle de estoque

Cada produto possui uma quantidade disponível no banco de dados.

Quando um pedido é finalizado, o sistema reduz automaticamente a quantidade comprada do estoque.

Caso não exista quantidade suficiente, o pedido não pode ser realizado.

Produtos sem estoque aparecem como **Esgotado** no site e não podem ser adicionados ao carrinho.

Se um pedido for cancelado pelo administrador, os produtos retornam automaticamente ao estoque.

---

## Segurança

O projeto utiliza algumas práticas de segurança, como:

- Senhas armazenadas utilizando `password_hash()`
- Verificação de senhas utilizando `password_verify()`
- Consultas ao banco utilizando PDO
- Prepared Statements
- Controle de acesso para páginas administrativas
- Controle de acesso aos pedidos de cada cliente
- Regeneração do ID da sessão após autenticação
- Validação do estoque durante a finalização do pedido
- Transações no banco para operações envolvendo pedidos e estoque

---

## Identidade visual

O Rino Cream utiliza uma identidade visual inspirada em sorveterias, com cores suaves e elementos relacionados a sorvetes.

Entre as principais cores utilizadas estão:

```text
Azul claro: #6EC6E8
Rosa:       #F58FA3
Baunilha:   #FFF7E8
Marrom:     #5A3E36
Azul:       #3185A8
Branco:     #FFFFFF
```

As principais fontes utilizadas são:

```text
Fredoka
Nunito
```

---

## Responsividade

O site foi desenvolvido para funcionar tanto em computadores quanto em dispositivos móveis.

Em telas menores, o menu principal é substituído por um menu mobile que pode ser aberto pelo botão de navegação.

---

## Projeto acadêmico

O Rino Cream foi desenvolvido como projeto acadêmico com o objetivo de aplicar conhecimentos de desenvolvimento web, programação em PHP, banco de dados, lógica de programação e criação de interfaces responsivas.
