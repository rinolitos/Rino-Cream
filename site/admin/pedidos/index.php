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

$titulo = "Pedidos | Rino Cream";
$prefixo = "../../";

$sql = "
    SELECT
        pedidos.idpedido,
        pedidos.data_pedido,
        pedidos.status,
        pedidos.valor_total,
        clientes.nome AS cliente,
        usuarios.email
    FROM pedidos
    INNER JOIN clientes
        ON clientes.idcliente = pedidos.idcliente
    INNER JOIN usuarios
        ON usuarios.idusuario = clientes.idusuario
    ORDER BY pedidos.idpedido DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$pedidos = $stmt->fetchAll();

$statusNomes = [
    "aguardando" => "Aguardando",
    "preparando" => "Preparando",
    "pronto" => "Pronto",
    "entregue" => "Entregue",
    "cancelado" => "Cancelado"
];

require_once "../../includes/header.php";

?>

<section class="carrinho-topo">

    <span>
        📦 Administração
    </span>

    <h1>
        Pedidos
    </h1>

    <p>
        Acompanhe e gerencie os pedidos realizados pelos clientes.
    </p>

</section>

<section class="carrinho">

    <?php if (count($pedidos) === 0): ?>

        <div class="carrinho-vazio">

            <span>
                📦
            </span>

            <h2>
                Nenhum pedido encontrado
            </h2>

            <p>
                Os pedidos realizados pelos clientes aparecerão aqui.
            </p>

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
                            Cliente:
                            <strong>
                                <?= htmlspecialchars($pedido["cliente"]) ?>
                            </strong>
                        </p>

                        <p>
                            E-mail:
                            <?= htmlspecialchars($pedido["email"]) ?>
                        </p>

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
                                <?= htmlspecialchars(
                                    $statusNomes[$pedido["status"]]
                                    ?? $pedido["status"]
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

                        <div class="admin-produto-acoes">

                            <a
                                href="detalhes.php?idpedido=<?= $pedido["idpedido"] ?>"
                                class="botao-editar-produto"
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

require_once "../../includes/footer.php";

?>