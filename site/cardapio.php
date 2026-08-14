<?php

session_start();

if (
    isset($_SESSION["usuario"]) &&
    $_SESSION["usuario"]["tipo"] === "admin"
) {

    header("Location: admin/");
    exit;
}

require_once "config/conexao.php";

$titulo = "Cardápio | Rino Cream";

$sqlCategorias = "
    SELECT *
    FROM categorias
    ORDER BY nome
";

$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();

$categorias = $stmtCategorias->fetchAll();

require_once "includes/header.php";

?>

<section class="cardapio-topo">

    <div>

        <span>
            🍨 Nosso cardápio
        </span>

        <h1>
            Escolha seus favoritos
        </h1>

        <p>
            Explore nossos sabores e encontre o que combina com você.
        </p>

    </div>

</section>

<section class="cardapio">

    <?php foreach ($categorias as $categoria): ?>

        <?php

        $sqlProdutos = "
            SELECT *
            FROM produtos
            WHERE idcategoria = :idcategoria
            AND ativo = 1
            ORDER BY nome
        ";

        $stmtProdutos = $pdo->prepare(
            $sqlProdutos
        );

        $stmtProdutos->execute([
            ":idcategoria" => $categoria["idcategoria"]
        ]);

        $produtos = $stmtProdutos->fetchAll();

        ?>

        <?php if (count($produtos) > 0): ?>

            <section class="categoria">

                <div class="categoria-titulo">

                    <h2>
                        <?= htmlspecialchars($categoria["nome"]) ?>
                    </h2>

                </div>

                <div class="produtos-grid">

                    <?php foreach ($produtos as $produto): ?>

                        <?php

                        $estoque = (int) $produto["estoque"];

                        ?>

                        <article
                            class="produto-card <?= $estoque <= 0 ? "produto-esgotado" : "" ?>"
                        >

                            <div class="produto-imagem">

                                <?php if (!empty($produto["imagem"])): ?>

                                    <img
                                        src="imagens/<?= htmlspecialchars($produto["imagem"]) ?>"
                                        alt="<?= htmlspecialchars($produto["nome"]) ?>"
                                    >

                                <?php else: ?>

                                    <span>
                                        🍦
                                    </span>

                                <?php endif; ?>

                                <?php if ($estoque <= 0): ?>

                                    <span class="produto-selo-esgotado">
                                        Esgotado
                                    </span>

                                <?php endif; ?>

                            </div>

                            <div class="produto-info">

                                <h3>
                                    <?= htmlspecialchars($produto["nome"]) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($produto["descricao"]) ?>
                                </p>

                                <?php if ($estoque > 0): ?>

                                    <span class="produto-estoque">

                                        <?php if ($estoque === 1): ?>

                                            1 unidade disponível

                                        <?php else: ?>

                                            <?= $estoque ?> unidades disponíveis

                                        <?php endif; ?>

                                    </span>

                                <?php else: ?>

                                    <span class="produto-estoque produto-estoque-esgotado">
                                        Produto indisponível no momento
                                    </span>

                                <?php endif; ?>

                                <div class="produto-footer">

                                    <strong>

                                        R$
                                        <?= number_format(
                                            $produto["preco"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>

                                    </strong>

                                    <?php if ($estoque > 0): ?>

                                        <form
                                            action="carrinho/adicionar.php"
                                            method="POST"
                                            class="form-adicionar-carrinho"
                                        >

                                            <input
                                                type="hidden"
                                                name="idproduto"
                                                value="<?= $produto["idproduto"] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="origem"
                                                value="cardapio"
                                            >

                                            <button
                                                type="submit"
                                                aria-label="Adicionar <?= htmlspecialchars($produto["nome"]) ?> ao carrinho"
                                            >
                                                +
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="botao-produto-esgotado"
                                            disabled
                                            aria-label="<?= htmlspecialchars($produto["nome"]) ?> esgotado"
                                        >
                                            ×
                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        <?php endif; ?>

    <?php endforeach; ?>

</section>

<?php

require_once "includes/footer.php";

?>