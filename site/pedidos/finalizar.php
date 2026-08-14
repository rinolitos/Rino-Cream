<?php

session_start();

require_once "../config/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../carrinho/");
    exit;
}

if (empty($_SESSION["carrinho"])) {

    $_SESSION["erro_carrinho"] = "Seu carrinho está vazio.";

    header("Location: ../carrinho/");
    exit;
}

if (!isset($_SESSION["usuario"])) {

    $_SESSION["destino_apos_login"] = "../carrinho/";

    header("Location: ../conta/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

if (
    $usuario["tipo"] !== "cliente" ||
    empty($usuario["idcliente"])
) {

    $_SESSION["erro_carrinho"] = "Esta conta não pode realizar pedidos.";

    header("Location: ../carrinho/");
    exit;
}

$idcliente = (int) $usuario["idcliente"];

$carrinho = $_SESSION["carrinho"];

try {

    $pdo->beginTransaction();

    $itensPedido = [];
    $valorTotal = 0;

    foreach ($carrinho as $idproduto => $quantidade) {

        $idproduto = (int) $idproduto;
        $quantidade = (int) $quantidade;

        if ($quantidade <= 0) {
            continue;
        }

        $sqlProduto = "
            SELECT
                idproduto,
                nome,
                preco,
                estoque,
                ativo
            FROM produtos
            WHERE idproduto = :idproduto
            FOR UPDATE
        ";

        $stmtProduto = $pdo->prepare($sqlProduto);

        $stmtProduto->execute([
            ":idproduto" => $idproduto
        ]);

        $produto = $stmtProduto->fetch();

        if (!$produto || !$produto["ativo"]) {

            throw new Exception(
                "Um dos produtos do carrinho não está mais disponível."
            );
        }

        if ($produto["estoque"] < $quantidade) {

            throw new Exception(
                "Não há estoque suficiente de " . $produto["nome"] . "."
            );
        }

        $precoUnitario = (float) $produto["preco"];

        $subtotal =
            $precoUnitario * $quantidade;

        $valorTotal += $subtotal;

        $itensPedido[] = [
            "idproduto" => $idproduto,
            "quantidade" => $quantidade,
            "preco_unitario" => $precoUnitario
        ];
    }

    if (count($itensPedido) === 0) {

        throw new Exception(
            "Não existem produtos válidos no carrinho."
        );
    }

    $sqlPedido = "
        INSERT INTO pedidos
        (
            idcliente,
            valor_total
        )
        VALUES
        (
            :idcliente,
            :valor_total
        )
    ";

    $stmtPedido =
        $pdo->prepare($sqlPedido);

    $stmtPedido->execute([
        ":idcliente" => $idcliente,
        ":valor_total" => $valorTotal
    ]);

    $idpedido =
        $pdo->lastInsertId();

    $sqlItem = "
        INSERT INTO itens_pedido
        (
            idpedido,
            idproduto,
            quantidade,
            preco_unitario
        )
        VALUES
        (
            :idpedido,
            :idproduto,
            :quantidade,
            :preco_unitario
        )
    ";

    $stmtItem =
        $pdo->prepare($sqlItem);

    $sqlEstoque = "
        UPDATE produtos
        SET estoque = estoque - :quantidade
        WHERE idproduto = :idproduto
    ";

    $stmtEstoque =
        $pdo->prepare($sqlEstoque);

    foreach ($itensPedido as $item) {

        $stmtItem->execute([
            ":idpedido" => $idpedido,
            ":idproduto" => $item["idproduto"],
            ":quantidade" => $item["quantidade"],
            ":preco_unitario" => $item["preco_unitario"]
        ]);

        $stmtEstoque->execute([
            ":quantidade" => $item["quantidade"],
            ":idproduto" => $item["idproduto"]
        ]);
    }

    $pdo->commit();

    unset($_SESSION["carrinho"]);

    header(
        "Location: sucesso.php?idpedido=" .
        $idpedido
    );

    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }

    $_SESSION["erro_carrinho"] =
        $e->getMessage();

    header("Location: ../carrinho/");
    exit;
}