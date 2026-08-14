<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Detalhes do pedido | Rino Cream";
$prefixo = "../";

if (!isset($_SESSION["usuario"])) {

    $_SESSION["destino_apos_login"] = "../pedidos/";

    header("Location: ../conta/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

if (
    $usuario["tipo"] !== "cliente" ||
    empty($usuario["idcliente"])
) {

    header("Location: ../index.php");
    exit;
}

$idcliente = (int) $usuario["idcliente"];

$idpedido = (int) ($_GET["idpedido"] ?? 0);

if ($idpedido <= 0) {

    header("Location: index.php");
    exit;
}

$sqlPedido = "
    SELECT
        idpedido,
        data_pedido,
        status,
        valor_total
    FROM pedidos
    WHERE idpedido = :idpedido
    AND idcliente = :idcliente
";

$stmtPedido = $pdo->prepare($sqlPedido);

$stmtPedido->execute([
    ":idpedido" => $idpedido,
    ":idcliente" => $idcliente
]);

$pedido = $stmtPedido->fetch();

if (!$pedido) {

    header("Location: index.php");
    exit;
}

$sqlItens = "
    SELECT
        itens_pedido.iditem,
        itens_pedido.idproduto,
        itens_pedido.quantidade,
        itens_pedido.preco_unitario,
        produtos.nome,
        produtos.imagem
    FROM itens_pedido
    INNER JOIN produtos
        ON produtos.idproduto = itens_pedido.idproduto
    WHERE itens_pedido.idpedido = :idpedido
    ORDER BY itens_pedido.iditem
";

$stmtItens = $pdo->prepare($sqlItens);

$stmtItens->execute([
    ":idpedido" => $idpedido
]);

$itens = $stmtItens->fetchAll();

require_once "../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        📦 Pedido #<?= $pedido["idpedido"] ?>
    </span>

    <h1>
        Detalhes do pedido
    </h1>

    <p>
        <?= date(
            "d/m/Y H:i",
            strtotime($pedido["data_pedido"])
        ) ?>
    </p>

</section>

<section class="carrinho">

    <div class="carrinho-resumo">

        <span>
            Status
        </span>

        <strong>
            <?= ucfirst(
                htmlspecialchars($pedido["status"])
            ) ?>
        </strong>

    </div>

    <div class="carrinho-lista">

        <?php foreach ($itens as $item): ?>

            <?php

            $subtotal =
                $item["preco_unitario"] *
                $item["quantidade"];

            ?>

            <article class="carrinho-item">

                <div class="carrinho-item-imagem">

                    <?php if (!empty($item["imagem"])): ?>

                        <img
                            src="../imagens/<?= htmlspecialchars($item["imagem"]) ?>"
                            alt="<?= htmlspecialchars($item["nome"]) ?>"
                        >

                    <?php else: ?>

                        <span>
                            🍦
                        </span>

                    <?php endif; ?>

                </div>

                <div class="carrinho-item-info">

                    <h2>
                        <?= htmlspecialchars($item["nome"]) ?>
                    </h2>

                    <p>
                        Preço unitário:

                        R$
                        <?= number_format(
                            $item["preco_unitario"],
                            2,
                            ",",
                            "."
                        ) ?>
                    </p>

                    <p>
                        Quantidade:

                        <strong>
                            <?= $item["quantidade"] ?>
                        </strong>
                    </p>

                    <strong class="subtotal">

                        Subtotal:

                        R$
                        <?= number_format(
                            $subtotal,
                            2,
                            ",",
                            "."
                        ) ?>

                    </strong>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <div class="carrinho-resumo">

        <span>
            Total do pedido
        </span>

        <strong>
            R$
            <?= number_format(
                $pedido["valor_total"],
                2,
                ",",
                "."
            ) ?>
        </strong>

        <a
            href="index.php"
            class="botao-principal"
        >
            Voltar para meus pedidos
        </a>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>