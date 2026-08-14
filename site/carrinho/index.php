<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Carrinho | Rino Cream";
$prefixo = "../";

$carrinho = $_SESSION["carrinho"] ?? [];

$produtosCarrinho = [];
$total = 0;

$erroCarrinho = $_SESSION["erro_carrinho"] ?? "";

unset($_SESSION["erro_carrinho"]);

foreach ($carrinho as $idproduto => $quantidade) {

    $sql = "
        SELECT *
        FROM produtos
        WHERE idproduto = :idproduto
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":idproduto" => $idproduto
    ]);

    $produto = $stmt->fetch();

    if (!$produto) {

        unset(
            $_SESSION["carrinho"][$idproduto]
        );

        continue;
    }

    if ((int) $produto["ativo"] !== 1) {

        unset(
            $_SESSION["carrinho"][$idproduto]
        );

        $erroCarrinho =
            "Um produto que estava no carrinho não está mais disponível.";

        continue;
    }

    $estoque = (int) $produto["estoque"];

    if ($estoque <= 0) {

        unset(
            $_SESSION["carrinho"][$idproduto]
        );

        $erroCarrinho =
            $produto["nome"] .
            " está esgotado e foi removido do carrinho.";

        continue;
    }

    if ($quantidade > $estoque) {

        $quantidade = $estoque;

        $_SESSION["carrinho"][$idproduto] =
            $estoque;

        $erroCarrinho =
            "A quantidade de " .
            $produto["nome"] .
            " foi ajustada para o estoque disponível.";
    }

    $subtotal =
        $produto["preco"] * $quantidade;

    $produto["quantidade"] =
        $quantidade;

    $produto["subtotal"] =
        $subtotal;

    $produtosCarrinho[] =
        $produto;

    $total += $subtotal;
}

require_once "../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        🛒 Seu pedido
    </span>

    <h1>
        Meu carrinho
    </h1>

    <p>
        Confira os produtos escolhidos antes de finalizar.
    </p>

</section>

<section class="carrinho">

    <?php if ($erroCarrinho !== ""): ?>

        <div class="mensagem-erro">
            <?= htmlspecialchars($erroCarrinho) ?>
        </div>

    <?php endif; ?>

    <?php if (count($produtosCarrinho) === 0): ?>

        <div class="carrinho-vazio">

            <span>
                🍦
            </span>

            <h2>
                Seu carrinho está vazio
            </h2>

            <p>
                Escolha alguns produtos no nosso cardápio.
            </p>

            <a
                href="../cardapio.php"
                class="botao-principal"
            >
                Ver cardápio
            </a>

        </div>

    <?php else: ?>

        <div class="carrinho-lista">

            <?php foreach ($produtosCarrinho as $produto): ?>

                <?php

                $estoque = (int) $produto["estoque"];

                $atingiuLimite =
                    $produto["quantidade"] >= $estoque;

                ?>

                <article class="carrinho-item">

                    <div class="carrinho-item-imagem">

                        <?php if (!empty($produto["imagem"])): ?>

                            <img
                                src="../imagens/<?= htmlspecialchars($produto["imagem"]) ?>"
                                alt="<?= htmlspecialchars($produto["nome"]) ?>"
                            >

                        <?php else: ?>

                            <span>
                                🍦
                            </span>

                        <?php endif; ?>

                    </div>

                    <div class="carrinho-item-info">

                        <h2>
                            <?= htmlspecialchars($produto["nome"]) ?>
                        </h2>

                        <p>
                            R$
                            <?= number_format(
                                $produto["preco"],
                                2,
                                ",",
                                "."
                            ) ?>
                        </p>

                        <span class="carrinho-estoque">

                            <?php if ($estoque === 1): ?>

                                1 unidade disponível

                            <?php else: ?>

                                <?= $estoque ?> unidades disponíveis

                            <?php endif; ?>

                        </span>

                        <div class="quantidade-controle">

                            <form
                                action="atualizar.php"
                                method="POST"
                            >

                                <input
                                    type="hidden"
                                    name="idproduto"
                                    value="<?= $produto["idproduto"] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="diminuir"
                                >

                                <button
                                    type="submit"
                                    aria-label="Diminuir quantidade"
                                >
                                    −
                                </button>

                            </form>

                            <strong>
                                <?= $produto["quantidade"] ?>
                            </strong>

                            <?php if (!$atingiuLimite): ?>

                                <form
                                    action="atualizar.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="idproduto"
                                        value="<?= $produto["idproduto"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="acao"
                                        value="aumentar"
                                    >

                                    <button
                                        type="submit"
                                        aria-label="Aumentar quantidade"
                                    >
                                        +
                                    </button>

                                </form>

                            <?php else: ?>

                                <button
                                    type="button"
                                    class="quantidade-limite"
                                    disabled
                                    title="Limite do estoque atingido"
                                    aria-label="Limite do estoque atingido"
                                >
                                    +
                                </button>

                            <?php endif; ?>

                        </div>

                        <?php if ($atingiuLimite): ?>

                            <span class="carrinho-limite-texto">
                                Limite do estoque atingido
                            </span>

                        <?php endif; ?>

                        <strong class="subtotal">

                            Subtotal:

                            R$
                            <?= number_format(
                                $produto["subtotal"],
                                2,
                                ",",
                                "."
                            ) ?>

                        </strong>

                        <form
                            action="atualizar.php"
                            method="POST"
                            class="form-remover"
                        >

                            <input
                                type="hidden"
                                name="idproduto"
                                value="<?= $produto["idproduto"] ?>"
                            >

                            <input
                                type="hidden"
                                name="acao"
                                value="remover"
                            >

                            <button
                                type="submit"
                                class="botao-remover"
                            >
                                Remover
                            </button>

                        </form>

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
                    $total,
                    2,
                    ",",
                    "."
                ) ?>
            </strong>

            <form
                action="../pedidos/finalizar.php"
                method="POST"
            >

                <button
                    type="submit"
                    class="botao-finalizar"
                >
                    Finalizar pedido
                </button>

            </form>

        </div>

    <?php endif; ?>

</section>

<?php

require_once "../includes/footer.php";

?>