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

$titulo = "Cadastrar produto | Rino Cream";
$prefixo = "../../";

$erro = "";

$nome = "";
$descricao = "";
$preco = "";
$estoque = "";
$imagem = "";
$idcategoria = "";

$sqlCategorias = "
    SELECT *
    FROM categorias
    ORDER BY nome
";

$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();

$categorias = $stmtCategorias->fetchAll();

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

        $sql = "
            INSERT INTO produtos
            (
                idcategoria,
                nome,
                descricao,
                preco,
                imagem,
                estoque,
                ativo
            )
            VALUES
            (
                :idcategoria,
                :nome,
                :descricao,
                :preco,
                :imagem,
                :estoque,
                1
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":idcategoria" => $idcategoria,
            ":nome" => $nome,
            ":descricao" => $descricao,
            ":preco" => $preco,
            ":imagem" => $imagem,
            ":estoque" => $estoque
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
                🍦 Administração
            </span>

            <h1>
                Cadastrar produto
            </h1>

            <p>
                Adicione um novo produto ao cardápio da Rino Cream.
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

            <div class="campo">

                <label for="nome">
                    Nome do produto
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome) ?>"
                    placeholder="Ex.: Sorvete de Pistache"
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
                    placeholder="Descrição do produto"
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

                    <option value="">
                        Selecione uma categoria
                    </option>

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
                    placeholder="Ex.: 9,50"
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
                    placeholder="Ex.: 50"
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
                    placeholder="Ex.: pistache.jpg"
                >

            </div>

            <button
                type="submit"
                class="botao-conta"
            >
                Cadastrar produto
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