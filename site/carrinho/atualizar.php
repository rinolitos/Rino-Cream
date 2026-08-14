<?php

session_start();

require_once "../config/conexao.php";

if (
    !isset($_POST["idproduto"]) ||
    !isset($_POST["acao"])
) {

    header("Location: index.php");
    exit;
}

$idproduto =
    (int) $_POST["idproduto"];

$acao =
    $_POST["acao"];

$acoesPermitidas = [
    "aumentar",
    "diminuir",
    "remover"
];

if (!in_array(
    $acao,
    $acoesPermitidas,
    true
)) {

    header("Location: index.php");
    exit;
}

if (
    !isset(
        $_SESSION["carrinho"][$idproduto]
    )
) {

    header("Location: index.php");
    exit;
}

if ($acao === "remover") {

    unset(
        $_SESSION["carrinho"][$idproduto]
    );

    header("Location: index.php");
    exit;
}

if ($acao === "diminuir") {

    $_SESSION["carrinho"][$idproduto]--;

    if (
        $_SESSION["carrinho"][$idproduto]
        <= 0
    ) {

        unset(
            $_SESSION["carrinho"][$idproduto]
        );
    }

    header("Location: index.php");
    exit;
}

$sql = "
    SELECT
        idproduto,
        nome,
        estoque,
        ativo
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

    $_SESSION["erro_carrinho"] =
        "O produto não está mais disponível.";

    header("Location: index.php");
    exit;
}

if ((int) $produto["ativo"] !== 1) {

    unset(
        $_SESSION["carrinho"][$idproduto]
    );

    $_SESSION["erro_carrinho"] =
        $produto["nome"] .
        " não está mais disponível.";

    header("Location: index.php");
    exit;
}

$estoque =
    (int) $produto["estoque"];

$quantidadeAtual =
    (int) $_SESSION["carrinho"][$idproduto];

if ($estoque <= 0) {

    unset(
        $_SESSION["carrinho"][$idproduto]
    );

    $_SESSION["erro_carrinho"] =
        $produto["nome"] .
        " está esgotado e foi removido do carrinho.";

    header("Location: index.php");
    exit;
}

if (
    $acao === "aumentar" &&
    $quantidadeAtual >= $estoque
) {

    $_SESSION["erro_carrinho"] =
        "Você já adicionou todas as unidades disponíveis de " .
        $produto["nome"] .
        ".";

    header("Location: index.php");
    exit;
}

if ($acao === "aumentar") {

    $_SESSION["carrinho"][$idproduto]++;
}

header("Location: index.php");
exit;