<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Meus pedidos | Rino Cream";
$prefixo = "../";

if (!isset($_SESSION["usuario"])) {

    $_SESSION["destino_apos_login"] = "../pedidos/";

    header("Location: ../conta/login.php");
    exit;
}

$usuario = $_SESSION["usuario"];

if (
    $usuario["tipo"] !== "cliente" ||
    empty($usuario["idcliente"])
) {

    header("Location: ../index.php");
    exit;
}

$idcliente = (int) $usuario["idcliente"];

$sql = "
    SELECT
        idpedido,
        data_pedido,
        status,
        valor_total
    FROM pedidos
    WHERE idcliente = :idcliente
    ORDER BY idpedido DESC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":idcliente" => $idcliente
]);

$pedidos = $stmt->fetchAll();

require_once "../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        📦 Histórico
    </span>

    <h1>
        Meus pedidos
    </h1>

    <p>
        Confira os pedidos que você já realizou na Rino Cream.
    </p>

</section>

<section class="carrinho">

    <?php if (count($pedidos) === 0): ?>

        <div class="carrinho-vazio">

            <span>
                🍦
            </span>

            <h2>
                Você ainda não fez nenhum pedido
            </h2>

            <p>
                Explore nosso cardápio e escolha seus sabores favoritos.
            </p>

            <a
                href="../cardapio.php"
                class="botao-principal"
            >
                Ver cardápio
            </a>

        </div>

    <?php else: ?>

        <div class="carrinho-lista">

            <?php foreach ($pedidos as $pedido): ?>

                <article class="carrinho-item">

                    <div class="carrinho-item-info">

                        <h2>
                            Pedido #<?= $pedido["idpedido"] ?>
                        </h2>

                        <p>
                            Data:
                            <?= date(
                                "d/m/Y H:i",
                                strtotime($pedido["data_pedido"])
                            ) ?>
                        </p>

                        <p>
                            Status:
                            <strong>
                                <?= ucfirst(
                                    htmlspecialchars($pedido["status"])
                                ) ?>
                            </strong>
                        </p>

                        <strong class="subtotal">

                            Total:

                            R$
                            <?= number_format(
                                $pedido["valor_total"],
                                2,
                                ",",
                                "."
                            ) ?>

                        </strong>

                        <div class="conta-link">

                            <a
                                href="detalhes.php?idpedido=<?= $pedido["idpedido"] ?>"
                                class="botao-principal"
                            >
                                Ver detalhes
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php

require_once "../includes/footer.php";

?>