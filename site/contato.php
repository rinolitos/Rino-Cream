<?php

session_start();

if (
    isset($_SESSION["usuario"]) &&
    $_SESSION["usuario"]["tipo"] === "admin"
) {
    $titulo = "Contato | Rino Cream";
} else {
    $titulo = "Contato | Rino Cream";
}

require_once "includes/header.php";

?>

<section class="cardapio-topo">

    <div>

        <span>
            📞 Fale com a gente
        </span>

        <h1>
            Contato
        </h1>

        <p>
            Tem alguma dúvida sobre a Rino Cream?
            Confira nossos canais de atendimento.
        </p>

    </div>

</section>

<section class="carrinho">

    <div class="carrinho-lista">

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    📱 Telefone
                </h2>

                <p>
                    (11) 4000-1234
                </p>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    ✉️ E-mail
                </h2>

                <p>
                    contato@rinocream.com
                </p>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    📍 Endereço
                </h2>

                <p>
                    Rua das Sobremesas, 100
                </p>

                <p>
                    Jundiaí - SP
                </p>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    📷 Instagram
                </h2>

                <p>
                    @rinocream
                </p>

            </div>

        </article>

        <article class="carrinho-item">

            <div class="carrinho-item-info">

                <h2>
                    🕐 Horário de funcionamento
                </h2>

                <p>
                    Segunda a sexta: 12h às 22h
                </p>

                <p>
                    Sábados e domingos: 11h às 23h
                </p>

            </div>

        </article>

    </div>

    <div class="carrinho-resumo">

        <span>
            🍦 Rino Cream
        </span>

        <strong>
            Estamos esperando por você!
        </strong>

        <p>
            Escolha seus sabores favoritos e monte seu pedido
            diretamente pelo nosso site.
        </p>

        <?php if (
            !isset($_SESSION["usuario"]) ||
            $_SESSION["usuario"]["tipo"] !== "admin"
        ): ?>

            <a
                href="cardapio.php"
                class="botao-principal"
            >
                Ver cardápio
            </a>

        <?php endif; ?>

    </div>

</section>

<?php

require_once "includes/footer.php";

?>