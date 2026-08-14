<?php

session_start();

require_once "../../config/conexao.php";

if (
    !isset($_SESSION["usuario"]) ||
    $_SESSION["usuario"]["tipo"] !== "admin"
) {
    header("Location: ../../conta/login.php");
    exit;
}

$titulo = "Detalhes do pedido | Rino Cream";
$prefixo = "../../";

$idpedido =
    (int) ($_GET["idpedido"] ?? 0);

if ($idpedido <= 0) {

    header("Location: index.php");
    exit;
}

$mensagem =
    $_SESSION["mensagem_admin_pedido"]
    ?? "";

$erro =
    $_SESSION["erro_admin_pedido"]
    ?? "";

unset(
    $_SESSION["mensagem_admin_pedido"],
    $_SESSION["erro_admin_pedido"]
);

$sqlPedido = "
    SELECT
        pedidos.idpedido,
        pedidos.data_pedido,
        pedidos.status,
        pedidos.valor_total,
        clientes.nome AS cliente,
        clientes.telefone,
        clientes.endereco,
        usuarios.email
    FROM pedidos
    INNER JOIN clientes
        ON clientes.idcliente =
            pedidos.idcliente
    INNER JOIN usuarios
        ON usuarios.idusuario =
            clientes.idusuario
    WHERE pedidos.idpedido =
        :idpedido
";

$stmtPedido =
    $pdo->prepare(
        $sqlPedido
    );

$stmtPedido->execute([
    ":idpedido" => $idpedido
]);

$pedido =
    $stmtPedido->fetch();

if (!$pedido) {

    header("Location: index.php");
    exit;
}

$sqlItens = "
    SELECT
        itens_pedido.iditem,
        itens_pedido.quantidade,
        itens_pedido.preco_unitario,
        produtos.nome,
        produtos.imagem
    FROM itens_pedido
    INNER JOIN produtos
        ON produtos.idproduto =
            itens_pedido.idproduto
    WHERE itens_pedido.idpedido =
        :idpedido
    ORDER BY itens_pedido.iditem
";

$stmtItens =
    $pdo->prepare(
        $sqlItens
    );

$stmtItens->execute([
    ":idpedido" => $idpedido
]);

$itens =
    $stmtItens->fetchAll();

$statusNomes = [

    "aguardando" =>
        "Aguardando",

    "preparando" =>
        "Preparando",

    "pronto" =>
        "Pronto",

    "entregue" =>
        "Entregue",

    "cancelado" =>
        "Cancelado"

];

$proximosStatus = [

    "aguardando" => [
        "preparando" => "Preparando",
        "cancelado" => "Cancelado"
    ],

    "preparando" => [
        "pronto" => "Pronto",
        "cancelado" => "Cancelado"
    ],

    "pronto" => [
        "entregue" => "Entregue",
        "cancelado" => "Cancelado"
    ]

];

require_once "../../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        📦 Pedido #<?= $pedido["idpedido"] ?>
    </span>

    <h1>
        Detalhes do pedido
    </h1>

    <p>
        Veja as informações da compra e atualize seu andamento.
    </p>

</section>

<section class="carrinho">

    <?php if ($mensagem !== ""): ?>

        <div class="carrinho-resumo">

            <span>
                ✓ Informação
            </span>

            <p>
                <?= htmlspecialchars($mensagem) ?>
            </p>

        </div>

    <?php endif; ?>

    <?php if ($erro !== ""): ?>

        <div class="mensagem-erro">

            <?= htmlspecialchars($erro) ?>

        </div>

    <?php endif; ?>

    <div class="carrinho-resumo">

        <span>
            Cliente
        </span>

        <strong>

            <?= htmlspecialchars(
                $pedido["cliente"]
            ) ?>

        </strong>

        <p>

            <strong>
                E-mail:
            </strong>

            <?= htmlspecialchars(
                $pedido["email"]
            ) ?>

        </p>

        <p>

            <strong>
                Telefone:
            </strong>

            <?= htmlspecialchars(
                $pedido["telefone"]
            ) ?>

        </p>

        <p>

            <strong>
                Endereço:
            </strong>

            <?= htmlspecialchars(
                $pedido["endereco"]
            ) ?>

        </p>

        <p>

            <strong>
                Data:
            </strong>

            <?= date(
                "d/m/Y H:i",
                strtotime(
                    $pedido["data_pedido"]
                )
            ) ?>

        </p>

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
                            src="../../imagens/<?= htmlspecialchars($item["imagem"]) ?>"
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

                        <?= htmlspecialchars(
                            $item["nome"]
                        ) ?>

                    </h2>

                    <p>

                        Preço unitário:

                        <strong>

                            R$
                            <?= number_format(
                                $item["preco_unitario"],
                                2,
                                ",",
                                "."
                            ) ?>

                        </strong>

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

    </div>

    <div class="admin-status-card">

        <div class="conta-cabecalho">

            <span>
                ⚙️ Andamento do pedido
            </span>

            <?php if (
                $pedido["status"] ===
                "cancelado"
            ): ?>

                <h1>
                    Pedido cancelado
                </h1>

                <p>

                    Status atual:

                    <strong>
                        Cancelado
                    </strong>

                </p>

                <p>
                    Os produtos deste pedido
                    já foram devolvidos ao estoque.
                </p>

            <?php elseif (
                $pedido["status"] ===
                "entregue"
            ): ?>

                <h1>
                    Pedido entregue
                </h1>

                <p>

                    Status atual:

                    <strong>
                        Entregue
                    </strong>

                </p>

                <p>
                    Este pedido foi concluído
                    e não pode mais ser alterado.
                </p>

            <?php else: ?>

                <h1>
                    Atualizar status
                </h1>

                <p>

                    Status atual:

                    <strong>

                        <?= htmlspecialchars(
                            $statusNomes[
                                $pedido["status"]
                            ]
                            ?? $pedido["status"]
                        ) ?>

                    </strong>

                </p>

            <?php endif; ?>

        </div>

        <?php if (
            isset(
                $proximosStatus[
                    $pedido["status"]
                ]
            )
        ): ?>

            <form
                action="status.php"
                method="POST"
                class="conta-formulario"
            >

                <input
                    type="hidden"
                    name="idpedido"
                    value="<?= $pedido["idpedido"] ?>"
                >

                <div class="campo">

                    <label for="status">
                        Novo status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <?php foreach (
                            $proximosStatus[
                                $pedido["status"]
                            ]
                            as $valor => $nome
                        ): ?>

                            <option
                                value="<?= htmlspecialchars($valor) ?>"
                            >
                                <?= htmlspecialchars($nome) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <button
                    type="submit"
                    class="botao-conta"
                >
                    Atualizar status
                </button>

            </form>

        <?php endif; ?>

        <div class="conta-link">

            <a href="index.php">
                Voltar para pedidos
            </a>

        </div>

    </div>

</section>

<?php

require_once "../../includes/footer.php";

?>