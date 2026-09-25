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

$idCategoria = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$sqlCategoria = "
    SELECT *
    FROM categorias
    WHERE idcategoria = :idcategoria
";

$stmtCategoria = $pdo->prepare($sqlCategoria);
$stmtCategoria->execute([":idcategoria" => $idCategoria]);

$categoria = $stmtCategoria->fetch();

if (!$categoria) {
    header("Location: cardapio.php");
    exit;
}

$titulo = htmlspecialchars($categoria["nome"]) . " | Rino Cream";

$sqlProdutos = "
    SELECT *
    FROM produtos
    WHERE idcategoria = :idcategoria
    AND ativo = 1
    ORDER BY nome
";

$stmtProdutos = $pdo->prepare($sqlProdutos);
$stmtProdutos->execute([":idcategoria" => $idCategoria]);

$produtos = $stmtProdutos->fetchAll();

$totalProdutos = count($produtos);

// Palavras usadas na marca d'água decorativa de cada slide
$palavraCategoria = mb_strtoupper($categoria["nome"], "UTF-8");
$palavrasMarca = [$palavraCategoria, "RINO CREAM"];

require_once "includes/header.php";

?>

<?php if ($totalProdutos === 0): ?>

    <section class="categoria-vazia">

        <span class="categoria-vazia-emoji">
            <?= rc_emoji_categoria($categoria["nome"]) ?>
        </span>

        <h1>
            <?= htmlspecialchars($categoria["nome"]) ?>
        </h1>

        <p>
            Nenhum produto disponível nesta categoria no momento.
        </p>

        <a href="cardapio.php" class="botao-secundario">
            Voltar às categorias
        </a>

    </section>

<?php else: ?>

    <section
        class="cs-secao"
        data-cs-secao
        data-cs-total="<?= $totalProdutos ?>"
    >

        <div class="cs-topo">

            <a href="cardapio.php" class="cs-voltar">
                ← Categorias
            </a>

            <h1>
                <?= rc_emoji_categoria($categoria["nome"]) ?>
                <?= htmlspecialchars($categoria["nome"]) ?>
            </h1>

        </div>

        <div class="cs-wrapper" data-cs-wrapper>

            <?php foreach ($produtos as $indice => $produto): ?>

                <?php

                $estoque = (int) $produto["estoque"];
                $tema = $indice % 4;

                ?>

                <article
                    class="cs-slide cs-tema-<?= $tema ?> <?= $indice === 0 ? "active" : "" ?>"
                    data-index="<?= $indice ?>"
                >

                    <div class="cs-watermark" aria-hidden="true">

                        <?php for ($linha = 0; $linha < 4; $linha++): ?>

                            <div class="cs-watermark-linha">

                                <?php for ($rep = 0; $rep < 3; $rep++): ?>

                                    <?= htmlspecialchars($palavrasMarca[$linha % 2]) ?>&nbsp;&nbsp;

                                <?php endfor; ?>

                            </div>

                        <?php endfor; ?>

                    </div>

                    <div class="cs-imagem-wrap">

                        <?php if (!empty($produto["imagem"])): ?>

                            <img
                                class="cs-imagem"
                                src="imagens/<?= htmlspecialchars($produto["imagem"]) ?>"
                                alt="<?= htmlspecialchars($produto["nome"]) ?>"
                                draggable="false"
                            >

                        <?php else: ?>

                            <span class="cs-imagem-emoji">
                                <?= rc_emoji_categoria($categoria["nome"]) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                    <div class="cs-card">

                        <span class="cs-card-tag">
                            <?= htmlspecialchars($categoria["nome"]) ?>
                        </span>

                        <h2 class="cs-card-nome">
                            <?= htmlspecialchars($produto["nome"]) ?>
                        </h2>

                        <p class="cs-card-desc">
                            <?= htmlspecialchars($produto["descricao"]) ?>
                        </p>

                        <div class="cs-card-divisor"></div>

                        <span class="cs-card-preco-label">
                            Preço
                        </span>

                        <span class="cs-card-preco">
                            R$
                            <?= number_format($produto["preco"], 2, ",", ".") ?>
                        </span>

                        <?php if ($estoque > 0): ?>

                            <span class="cs-card-estoque">

                                <?php if ($estoque === 1): ?>

                                    1 unidade disponível

                                <?php else: ?>

                                    <?= $estoque ?> unidades disponíveis

                                <?php endif; ?>

                            </span>

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
                                    value="categoria"
                                >

                                <button type="submit" class="cs-card-botao">
                                    Adicionar ao carrinho
                                </button>

                            </form>

                        <?php else: ?>

                            <span class="cs-card-estoque cs-card-estoque-esgotado">
                                Produto indisponível no momento
                            </span>

                            <button type="button" class="cs-card-botao" disabled>
                                Esgotado
                            </button>

                        <?php endif; ?>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

        <div class="cs-continuar" data-cs-continuar></div>

        <div class="cs-marquee" aria-hidden="true">

            <div class="cs-marquee-pista">

                <img src="imagens/banner-novidades.png" alt="" draggable="false">
                <img src="imagens/banner-novidades.png" alt="" draggable="false">

            </div>

        </div>

        <?php if ($totalProdutos > 1): ?>

            <div class="cs-dots" data-cs-dots>

                <?php foreach ($produtos as $indice => $produto): ?>

                    <button
                        type="button"
                        class="cs-dot <?= $indice === 0 ? "active" : "" ?>"
                        data-target="<?= $indice ?>"
                        aria-label="Ver <?= htmlspecialchars($produto["nome"]) ?>"
                    ></button>

                <?php endforeach; ?>

            </div>

            <button
                type="button"
                class="cs-seta cs-seta-esq escondido"
                data-cs-seta-esq
                aria-label="Produto anterior"
            >
                ←
            </button>

            <button
                type="button"
                class="cs-seta cs-seta-dir"
                data-cs-seta-dir
                aria-label="Próximo produto"
            >
                →
            </button>

            <div class="cs-dica" data-cs-dica>
                role para navegar
            </div>

        <?php endif; ?>

    </section>

<?php endif; ?>

<?php

require_once "includes/footer.php";

?>
