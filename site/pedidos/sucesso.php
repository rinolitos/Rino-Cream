<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Pedido realizado | Rino Cream";
$prefixo = "../";

if (!isset($_SESSION["usuario"])) {

    header("Location: ../conta/login.php");
    exit;
}

$idcliente = (int) (
    $_SESSION["usuario"]["idcliente"] ?? 0
);

$idpedido = (int) (
    $_GET["idpedido"] ?? 0
);

$sql = "
    SELECT
        idpedido,
        data_pedido,
        status,
        valor_total
    FROM pedidos
    WHERE idpedido = :idpedido
    AND idcliente = :idcliente
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":idpedido" => $idpedido,
    ":idcliente" => $idcliente
]);

$pedido = $stmt->fetch();

if (!$pedido) {

    header("Location: ../index.php");
    exit;
}

require_once "../includes/header.php";

?>

<section class="conta-pagina">

    <div class="conta-card conta-card-login">

        <div class="conta-cabecalho">

            <span>
                ✅ Pedido confirmado
            </span>

            <h1>
                Pedido realizado!
            </h1>

            <p>
                Seu pedido foi registrado com sucesso.
            </p>

        </div>

        <div class="carrinho-resumo">

            <span>
                Pedido
            </span>

            <strong>
                #<?= $pedido["idpedido"] ?>
            </strong>

            <p>
                Status:
                <strong>
                    <?= ucfirst(
                        htmlspecialchars($pedido["status"])
                    ) ?>
                </strong>
            </p>

            <p>
                Valor total:
            </p>

            <strong>
                R$
                <?= number_format(
                    $pedido["valor_total"],
                    2,
                    ",",
                    "."
                ) ?>
            </strong>

        </div>

        <div class="conta-link">

            <a
                href="../cardapio.php"
                class="botao-principal"
            >
                Continuar comprando
            </a>

        </div>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>