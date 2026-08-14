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

if (
    !isset($_POST["idproduto"]) ||
    !isset($_POST["acao"])
) {
    header("Location: index.php");
    exit;
}

$idproduto = (int) $_POST["idproduto"];
$acao = $_POST["acao"];

if ($acao === "desativar") {

    $ativo = 0;

} elseif ($acao === "ativar") {

    $ativo = 1;

} else {

    header("Location: index.php");
    exit;
}

$sql = "
    UPDATE produtos
    SET ativo = :ativo
    WHERE idproduto = :idproduto
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":ativo" => $ativo,
    ":idproduto" => $idproduto
]);

header("Location: index.php");
exit;