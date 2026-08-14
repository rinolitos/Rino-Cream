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
    !isset($_POST["idpedido"]) ||
    !isset($_POST["status"])
) {
    header("Location: index.php");
    exit;
}

$idpedido = (int) $_POST["idpedido"];
$statusNovo = $_POST["status"];

$statusPermitidos = [
    "aguardando",
    "preparando",
    "pronto",
    "entregue",
    "cancelado"
];

if (
    $idpedido <= 0 ||
    !in_array(
        $statusNovo,
        $statusPermitidos,
        true
    )
) {
    header("Location: index.php");
    exit;
}

try {

    $pdo->beginTransaction();

    $sqlPedido = "
        SELECT
            idpedido,
            status
        FROM pedidos
        WHERE idpedido = :idpedido
        FOR UPDATE
    ";

    $stmtPedido = $pdo->prepare(
        $sqlPedido
    );

    $stmtPedido->execute([
        ":idpedido" => $idpedido
    ]);

    $pedido = $stmtPedido->fetch();

    if (!$pedido) {

        $pdo->rollBack();

        header("Location: index.php");
        exit;
    }

    $statusAtual =
        $pedido["status"];

    if ($statusAtual === "cancelado") {

        $pdo->rollBack();

        $_SESSION["erro_admin_pedido"] =
            "Este pedido já foi cancelado e não pode mais ser alterado.";

        header(
            "Location: detalhes.php?idpedido=" .
            $idpedido
        );

        exit;
    }

    if ($statusAtual === "entregue") {

        $pdo->rollBack();

        $_SESSION["erro_admin_pedido"] =
            "Este pedido já foi entregue e não pode mais ser alterado.";

        header(
            "Location: detalhes.php?idpedido=" .
            $idpedido
        );

        exit;
    }

    if ($statusNovo === $statusAtual) {

        $pdo->rollBack();

        $_SESSION["mensagem_admin_pedido"] =
            "O pedido já está com esse status.";

        header(
            "Location: detalhes.php?idpedido=" .
            $idpedido
        );

        exit;
    }

    $transicoesPermitidas = [

        "aguardando" => [
            "preparando",
            "cancelado"
        ],

        "preparando" => [
            "pronto",
            "cancelado"
        ],

        "pronto" => [
            "entregue",
            "cancelado"
        ]

    ];

    $proximosStatus =
        $transicoesPermitidas[
            $statusAtual
        ] ?? [];

    if (
        !in_array(
            $statusNovo,
            $proximosStatus,
            true
        )
    ) {

        $pdo->rollBack();

        $_SESSION["erro_admin_pedido"] =
            "Essa alteração de status não é permitida.";

        header(
            "Location: detalhes.php?idpedido=" .
            $idpedido
        );

        exit;
    }

    if ($statusNovo === "cancelado") {

        $sqlItens = "
            SELECT
                idproduto,
                quantidade
            FROM itens_pedido
            WHERE idpedido = :idpedido
        ";

        $stmtItens = $pdo->prepare(
            $sqlItens
        );

        $stmtItens->execute([
            ":idpedido" => $idpedido
        ]);

        $itens =
            $stmtItens->fetchAll();

        foreach ($itens as $item) {

            $sqlEstoque = "
                UPDATE produtos
                SET estoque =
                    estoque + :quantidade
                WHERE idproduto = :idproduto
            ";

            $stmtEstoque =
                $pdo->prepare(
                    $sqlEstoque
                );

            $stmtEstoque->execute([
                ":quantidade" =>
                    (int) $item["quantidade"],

                ":idproduto" =>
                    (int) $item["idproduto"]
            ]);
        }
    }

    $sqlAtualizar = "
        UPDATE pedidos
        SET status = :status
        WHERE idpedido = :idpedido
    ";

    $stmtAtualizar =
        $pdo->prepare(
            $sqlAtualizar
        );

    $stmtAtualizar->execute([
        ":status" => $statusNovo,
        ":idpedido" => $idpedido
    ]);

    $pdo->commit();

    if ($statusNovo === "cancelado") {

        $_SESSION["mensagem_admin_pedido"] =
            "Pedido cancelado. Os produtos foram devolvidos ao estoque.";

    } elseif ($statusNovo === "entregue") {

        $_SESSION["mensagem_admin_pedido"] =
            "Pedido marcado como entregue.";

    } else {

        $_SESSION["mensagem_admin_pedido"] =
            "Status do pedido atualizado com sucesso.";
    }

} catch (Throwable $erro) {

    if ($pdo->inTransaction()) {

        $pdo->rollBack();
    }

    $_SESSION["erro_admin_pedido"] =
        "Não foi possível atualizar o pedido.";
}

header(
    "Location: detalhes.php?idpedido=" .
    $idpedido
);

exit;