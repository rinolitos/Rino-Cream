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

$titulo = "Início | Rino Cream";

$sql = "
    SELECT *
    FROM produtos
    WHERE ativo = 1
    LIMIT 6
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$produtos = $stmt->fetchAll();

require_once "includes/header.php";

?>

<section class="hero">

    <div class="hero-conteudo">

        <span class="hero-tag">
            🍦 Feito para adoçar seu dia
        </span>

        <h1>
            O sabor que combina
            com o seu momento.
        </h1>

        <p>
            Descubra sabores incríveis e monte seu pedido
            do seu jeito na Rino Cream.
        </p>

        <a
            href="cardapio.php"
            class="botao-principal"
        >
            Ver cardápio
        </a>

    </div>

</section>

<section class="produtos-destaque">

    <div class="secao-titulo">

        <span>
            🍨 Nossos favoritos
        </span>

        <h2>
            Experimente nossos sabores
        </h2>

        <p>
            Algumas das opções que fazem parte do nosso cardápio.
        </p>

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
                                    value="inicio"
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

    <a
        href="cardapio.php"
        class="botao-secundario"
    >
        Ver todos os produtos
    </a>

</section>

<section class="sobre-destaque">

    <div>

        <span>
            ✨ Por que Rino Cream?
        </span>

        <h2>
            Seu sorvete,
            do seu jeito.
        </h2>

        <p>
            Na Rino Cream, você encontra diferentes sabores e
            opções para escolher. Monte seu pedido de forma
            simples e aproveite cada momento.
        </p>

    </div>

</section>

<?php

require_once "includes/footer.php";

?>