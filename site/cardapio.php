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
    SELECT
        c.*,
        (
            SELECT COUNT(*)
            FROM produtos p
            WHERE p.idcategoria = c.idcategoria
            AND p.ativo = 1
        ) AS total_produtos,
        (
            SELECT p2.imagem
            FROM produtos p2
            WHERE p2.idcategoria = c.idcategoria
            AND p2.ativo = 1
            AND p2.imagem IS NOT NULL
            AND p2.imagem <> ''
            ORDER BY p2.idproduto
            LIMIT 1
        ) AS imagem_exemplo
    FROM categorias c
    ORDER BY c.nome
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
            Escolha uma categoria
        </h1>

        <p>
            Cada categoria abre em uma vitrine própria — deslize para o lado
            e explore os sabores como quem folheia uma revista.
        </p>

    </div>

</section>

<section class="cardapio-categorias">

    <div class="categorias-grid categorias-grid-grande">

        <?php foreach ($categorias as $categoria): ?>

            <?php $temProdutos = (int) $categoria["total_produtos"] > 0; ?>

            <a
                class="categoria-card categoria-card-grande <?= !$temProdutos ? "categoria-card-vazia" : "" ?>"
                href="<?= $temProdutos ? "categoria.php?id=" . (int) $categoria["idcategoria"] : "#" ?>"
                <?= !$temProdutos ? 'aria-disabled="true" tabindex="-1"' : "" ?>
            >

                <?php if (!empty($categoria["imagem_exemplo"])): ?>

                    <div class="categoria-card-foto">

                        <img
                            src="imagens/<?= htmlspecialchars($categoria["imagem_exemplo"]) ?>"
                            alt="<?= htmlspecialchars($categoria["nome"]) ?>"
                        >

                    </div>

                <?php endif; ?>

                <span class="categoria-card-emoji">
                    <?= rc_emoji_categoria($categoria["nome"]) ?>
                </span>

                <h3>
                    <?= htmlspecialchars($categoria["nome"]) ?>
                </h3>

                <span class="categoria-card-link">

                    <?php if ($temProdutos): ?>

                        <?= (int) $categoria["total_produtos"] ?>
                        <?= (int) $categoria["total_produtos"] === 1 ? "opção" : "opções" ?>
                        →

                    <?php else: ?>

                        Em breve

                    <?php endif; ?>

                </span>

            </a>

        <?php endforeach; ?>

    </div>

</section>

<?php

require_once "includes/footer.php";

?>
