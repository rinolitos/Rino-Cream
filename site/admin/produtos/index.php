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

$titulo = "Produtos | Rino Cream";
$prefixo = "../../";

$sql = "
    SELECT
        produtos.idproduto,
        produtos.nome,
        produtos.descricao,
        produtos.preco,
        produtos.estoque,
        produtos.imagem,
        produtos.ativo,
        categorias.nome AS categoria
    FROM produtos
    INNER JOIN categorias
        ON categorias.idcategoria = produtos.idcategoria
    ORDER BY produtos.ativo DESC, produtos.nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$produtos = $stmt->fetchAll();

require_once "../../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        🍦 Administração
    </span>

    <h1>
        Produtos
    </h1>

    <p>
        Gerencie os produtos disponíveis na Rino Cream.
    </p>

</section>

<section class="carrinho">

    <div class="carrinho-resumo">

        <span>
            Cardápio
        </span>

        <strong>
            <?= count($produtos) ?> produtos
        </strong>

        <a
            href="cadastrar.php"
            class="botao-principal"
        >
            + Cadastrar produto
        </a>

    </div>

    <div class="carrinho-lista admin-lista-produtos">

        <?php foreach ($produtos as $produto): ?>

            <article class="carrinho-item admin-produto-item">

                <div class="carrinho-item-imagem">

                    <?php if (!empty($produto["imagem"])): ?>

                        <img
                            src="../../imagens/<?= htmlspecialchars($produto["imagem"]) ?>"
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
                        Categoria:
                        <strong>
                            <?= htmlspecialchars($produto["categoria"]) ?>
                        </strong>
                    </p>

                    <p>
                        Preço:
                        <strong>
                            R$
                            <?= number_format(
                                $produto["preco"],
                                2,
                                ",",
                                "."
                            ) ?>
                        </strong>
                    </p>

                    <p>
                        Estoque:
                        <strong>
                            <?= $produto["estoque"] ?>
                        </strong>
                    </p>

                    <p>
                        Status:

                        <?php if ($produto["ativo"]): ?>

                            <strong class="status-ativo">
                                Ativo
                            </strong>

                        <?php else: ?>

                            <strong class="status-inativo">
                                Inativo
                            </strong>

                        <?php endif; ?>

                    </p>

                    <div class="admin-produto-acoes">

                        <a
                            href="editar.php?idproduto=<?= $produto["idproduto"] ?>"
                            class="botao-editar-produto"
                        >
                            Editar
                        </a>

                        <form
                            action="status.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="idproduto"
                                value="<?= $produto["idproduto"] ?>"
                            >

                            <?php if ($produto["ativo"]): ?>

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="desativar"
                                >

                                <button
                                    type="submit"
                                    class="botao-desativar-produto"
                                >
                                    Desativar
                                </button>

                            <?php else: ?>

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="ativar"
                                >

                                <button
                                    type="submit"
                                    class="botao-reativar-produto"
                                >
                                    Reativar
                                </button>

                            <?php endif; ?>

                        </form>

                    </div>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

</section>

<?php

require_once "../../includes/footer.php";

?>