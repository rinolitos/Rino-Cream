<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Painel Administrativo | Rino Cream";
$prefixo = "../";

if (
    !isset($_SESSION["usuario"]) ||
    $_SESSION["usuario"]["tipo"] !== "admin"
) {
    header("Location: ../conta/login.php");
    exit;
}

$sqlProdutos = "
    SELECT COUNT(*) AS total
    FROM produtos
    WHERE ativo = 1
";

$stmtProdutos = $pdo->prepare($sqlProdutos);
$stmtProdutos->execute();

$totalProdutos =
    $stmtProdutos->fetch()["total"];

$sqlPedidos = "
    SELECT COUNT(*) AS total
    FROM pedidos
";

$stmtPedidos = $pdo->prepare($sqlPedidos);
$stmtPedidos->execute();

$totalPedidos =
    $stmtPedidos->fetch()["total"];

$sqlAguardando = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'aguardando'
";

$stmtAguardando = $pdo->prepare($sqlAguardando);
$stmtAguardando->execute();

$totalAguardando =
    $stmtAguardando->fetch()["total"];

require_once "../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        ⚙️ Administração
    </span>

    <h1>
        Painel Rino Cream
    </h1>

    <p>
        Gerencie os produtos e pedidos da sorveteria.
    </p>

</section>

<section class="carrinho">

    <div class="carrinho-lista">

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    🍦 Produtos ativos
                </h2>

                <strong class="subtotal">
                    <?= $totalProdutos ?>
                </strong>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    📦 Total de pedidos
                </h2>

                <strong class="subtotal">
                    <?= $totalPedidos ?>
                </strong>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    ⏳ Pedidos aguardando
                </h2>

                <strong class="subtotal">
                    <?= $totalAguardando ?>
                </strong>

            </div>

        </article>

    </div>

    <div class="carrinho-resumo">

        <span>
            Produtos
        </span>

        <strong>
            Gerenciamento do cardápio
        </strong>

        <p>
            Cadastre, edite e controle os produtos disponíveis.
        </p>

        <a
            href="produtos/"
            class="botao-principal"
        >
            Gerenciar produtos
        </a>

    </div>

    <div class="carrinho-resumo">

        <span>
            Pedidos
        </span>

        <strong>
            Gerenciamento de pedidos
        </strong>

        <p>
            Visualize os pedidos dos clientes e atualize seus status.
        </p>

        <a
            href="pedidos/"
            class="botao-principal"
        >
            Gerenciar pedidos
        </a>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>