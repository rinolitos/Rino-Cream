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

$titulo = "Editar produto | Rino Cream";
$prefixo = "../../";

$erro = "";

$idproduto = (int) ($_GET["idproduto"] ?? $_POST["idproduto"] ?? 0);

if ($idproduto <= 0) {

    header("Location: index.php");
    exit;
}

$sqlProduto = "
    SELECT *
    FROM produtos
    WHERE idproduto = :idproduto
";

$stmtProduto = $pdo->prepare($sqlProduto);

$stmtProduto->execute([
    ":idproduto" => $idproduto
]);

$produto = $stmtProduto->fetch();

if (!$produto) {

    header("Location: index.php");
    exit;
}

$sqlCategorias = "
    SELECT *
    FROM categorias
    ORDER BY nome
";

$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();

$categorias = $stmtCategorias->fetchAll();

$nome = $produto["nome"];
$descricao = $produto["descricao"];
$preco = $produto["preco"];
$estoque = $produto["estoque"];
$imagem = $produto["imagem"];
$idcategoria = $produto["idcategoria"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $preco = str_replace(",", ".", trim($_POST["preco"] ?? ""));
    $estoque = trim($_POST["estoque"] ?? "");
    $imagem = trim($_POST["imagem"] ?? "");
    $idcategoria = (int) ($_POST["idcategoria"] ?? 0);

    if (
        $nome === "" ||
        $descricao === "" ||
        $preco === "" ||
        $estoque === "" ||
        $idcategoria <= 0
    ) {

        $erro = "Preencha todos os campos obrigatórios.";

    } elseif (!is_numeric($preco) || $preco <= 0) {

        $erro = "Digite um preço válido.";

    } elseif (
        filter_var(
            $estoque,
            FILTER_VALIDATE_INT
        ) === false ||
        $estoque < 0
    ) {

        $erro = "Digite um estoque válido.";

    } else {

        $sqlAtualizar = "
            UPDATE produtos
            SET
                idcategoria = :idcategoria,
                nome = :nome,
                descricao = :descricao,
                preco = :preco,
                estoque = :estoque,
                imagem = :imagem
            WHERE idproduto = :idproduto
        ";

        $stmtAtualizar = $pdo->prepare($sqlAtualizar);

        $stmtAtualizar->execute([
            ":idcategoria" => $idcategoria,
            ":nome" => $nome,
            ":descricao" => $descricao,
            ":preco" => $preco,
            ":estoque" => $estoque,
            ":imagem" => $imagem,
            ":idproduto" => $idproduto
        ]);

        header("Location: index.php");
        exit;
    }
}

require_once "../../includes/header.php";

?>

<section class="conta-pagina">

    <div class="conta-card">

        <div class="conta-cabecalho">

            <span>
                ✏️ Administração
            </span>

            <h1>
                Editar produto
            </h1>

            <p>
                Atualize as informações do produto.
            </p>

        </div>

        <?php if ($erro !== ""): ?>

            <div class="mensagem-erro">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            class="conta-formulario"
        >

            <input
                type="hidden"
                name="idproduto"
                value="<?= $idproduto ?>"
            >

            <div class="campo">

                <label for="nome">
                    Nome do produto
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label for="descricao">
                    Descrição
                </label>

                <input
                    type="text"
                    id="descricao"
                    name="descricao"
                    value="<?= htmlspecialchars($descricao) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label for="idcategoria">
                    Categoria
                </label>

                <select
                    id="idcategoria"
                    name="idcategoria"
                    required
                >

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?= $categoria["idcategoria"] ?>"
                            <?= $idcategoria == $categoria["idcategoria"] ? "selected" : "" ?>
                        >
                            <?= htmlspecialchars($categoria["nome"]) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="campo">

                <label for="preco">
                    Preço
                </label>

                <input
                    type="text"
                    id="preco"
                    name="preco"
                    value="<?= htmlspecialchars($preco) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label for="estoque">
                    Estoque
                </label>

                <input
                    type="number"
                    id="estoque"
                    name="estoque"
                    min="0"
                    value="<?= htmlspecialchars($estoque) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label for="imagem">
                    Nome da imagem
                </label>

                <input
                    type="text"
                    id="imagem"
                    name="imagem"
                    value="<?= htmlspecialchars($imagem) ?>"
                    placeholder="Ex.: chocolate.jpg"
                >

            </div>

            <button
                type="submit"
                class="botao-conta"
            >
                Salvar alterações
            </button>

        </form>

        <div class="conta-link">

            <a href="index.php">
                Voltar para produtos
            </a>

        </div>

    </div>

</section>

<?php

require_once "../../includes/footer.php";

?>