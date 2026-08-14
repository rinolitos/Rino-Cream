<?php

session_start();

require_once "../config/conexao.php";

if (!isset($_POST["idproduto"])) {

    header("Location: ../cardapio.php");
    exit;
}

$idproduto = (int) $_POST["idproduto"];

$sql = "
    SELECT
        idproduto,
        nome,
        estoque
    FROM produtos
    WHERE idproduto = :idproduto
    AND ativo = 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":idproduto" => $idproduto
]);

$produto = $stmt->fetch();

$ehAjax =
    isset($_POST["ajax"]) &&
    $_POST["ajax"] === "1";

if (!$produto) {

    if ($ehAjax) {

        header(
            "Content-Type: application/json; charset=utf-8"
        );

        http_response_code(404);

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Produto não encontrado."
        ]);

        exit;
    }

    header("Location: ../cardapio.php");
    exit;
}

if (!isset($_SESSION["carrinho"])) {

    $_SESSION["carrinho"] = [];
}

$quantidadeAtual =
    $_SESSION["carrinho"][$idproduto] ?? 0;

$estoqueDisponivel =
    (int) $produto["estoque"];

if ($estoqueDisponivel <= 0) {

    if ($ehAjax) {

        header(
            "Content-Type: application/json; charset=utf-8"
        );

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Esgotado",
            "quantidade" => array_sum(
                $_SESSION["carrinho"]
            )
        ]);

        exit;
    }

    header("Location: ../cardapio.php");
    exit;
}

if ($quantidadeAtual >= $estoqueDisponivel) {

    if ($ehAjax) {

        header(
            "Content-Type: application/json; charset=utf-8"
        );

        echo json_encode([
            "sucesso" => false,
            "mensagem" => "Limite do estoque",
            "quantidade" => array_sum(
                $_SESSION["carrinho"]
            )
        ]);

        exit;
    }

    header("Location: ../carrinho/");
    exit;
}

$_SESSION["carrinho"][$idproduto] =
    $quantidadeAtual + 1;

$quantidadeCarrinho = array_sum(
    $_SESSION["carrinho"]
);

if ($ehAjax) {

    header(
        "Content-Type: application/json; charset=utf-8"
    );

    header("Cache-Control: no-store");

    echo json_encode([
        "sucesso" => true,
        "quantidade" => $quantidadeCarrinho,
        "quantidadeProduto" =>
            $_SESSION["carrinho"][$idproduto],
        "estoque" => $estoqueDisponivel
    ]);

    exit;
}

$origem = $_POST["origem"] ?? "cardapio";

if ($origem === "inicio") {

    header("Location: ../index.php");

} else {

    header("Location: ../cardapio.php");
}

exit;