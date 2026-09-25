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

        <div class="hero-acoes">

            <a
                href="cardapio.php"
                class="botao-principal"
            >
                Ver cardápio
            </a>

        </div>

    </div>

</section>

<section class="marquee-secao" id="novidades" aria-label="Novidades">

    <div class="marquee-pista">

        <img
            src="imagens/banner-novidades.png"
            alt="Novidades Rino Cream"
            draggable="false"
        >

        <img
            src="imagens/banner-novidades.png"
            alt=""
            aria-hidden="true"
            draggable="false"
        >

    </div>

</section>

<section class="categorias-destaque" aria-label="Categorias">

    <div class="secao-titulo">

        <span>
            🍨 Explore por categoria
        </span>

        <h2>
            Encontre seu sabor favorito
        </h2>

        <p>
            Cada categoria tem sua própria vitrine, deslize para o lado e escolha o seu.
        </p>

    </div>

    <div class="categorias-grid">

        <?php foreach ($categorias as $categoria): ?>

            <a
                class="categoria-card"
                href="categoria.php?id=<?= (int) $categoria["idcategoria"] ?>"
            >

                <span class="categoria-card-emoji">
                    <?= rc_emoji_categoria($categoria["nome"]) ?>
                </span>

                <h3>
                    <?= htmlspecialchars($categoria["nome"]) ?>
                </h3>

                <span class="categoria-card-link">
                    Ver produtos →
                </span>

            </a>

        <?php endforeach; ?>

    </div>

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
