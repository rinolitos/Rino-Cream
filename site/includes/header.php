<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($titulo)) {
    $titulo = "Rino Cream";
}

if (!isset($prefixo)) {
    $prefixo = "";
}

if (!function_exists("rc_emoji_categoria")) {

    function rc_emoji_categoria($nomeCategoria) {

        $mapa = [
            "Sorvetes" => "🍦",
            "Milk-shakes" => "🥤",
            "Açaí" => "🍇",
            "Sobremesas" => "🍰",
        ];

        return $mapa[$nomeCategoria] ?? "🍨";
    }
}

$usuarioLogado = $_SESSION["usuario"] ?? null;

$primeiroNome = "";

if ($usuarioLogado) {

    $nomeCompleto = trim($usuarioLogado["nome"]);

    $partesNome = explode(" ", $nomeCompleto);

    $primeiroNome = $partesNome[0];
}

$ehAdmin =
    $usuarioLogado &&
    $usuarioLogado["tipo"] === "admin";

$linkLogo = $ehAdmin
    ? $prefixo . "admin/"
    : $prefixo . "index.php";

$quantidadeCarrinho = 0;

if (!empty($_SESSION["carrinho"])) {

    $quantidadeCarrinho = array_sum(
        $_SESSION["carrinho"]
    );
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($titulo) ?>
    </title>

    <link
        rel="icon"
        type="image/png"
        href="<?= $prefixo ?>imagens/logo.png"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="<?= $prefixo ?>css/style.css"
    >

    <link
        rel="stylesheet"
        href="<?= $prefixo ?>css/header.css"
    >

</head>

<body>

<header class="rc-header">

    <div class="rc-header__container">

        <a
            href="<?= $linkLogo ?>"
            class="rc-logo"
        >

            <img
                src="<?= $prefixo ?>imagens/logo.png"
                alt="Logo Rino Cream"
            >

            <span>
                Rino Cream
            </span>

        </a>

        <div class="rc-desktop">

            <nav class="rc-desktop-nav">

                <?php if ($ehAdmin): ?>

                    <a href="<?= $prefixo ?>admin/">
                        Painel
                    </a>

                    <a href="<?= $prefixo ?>admin/produtos/">
                        Produtos
                    </a>

                    <a href="<?= $prefixo ?>admin/pedidos/">
                        Pedidos
                    </a>

                    <a href="<?= $prefixo ?>contato.php">
                        Contato
                    </a>

                <?php else: ?>

                    <a href="<?= $prefixo ?>index.php">
                        Início
                    </a>

                    <a href="<?= $prefixo ?>cardapio.php">
                        Cardápio
                    </a>

                    <?php if (
                        $usuarioLogado &&
                        $usuarioLogado["tipo"] === "cliente"
                    ): ?>

                        <a href="<?= $prefixo ?>pedidos/">
                            Meus pedidos
                        </a>

                    <?php endif; ?>

                    <a href="<?= $prefixo ?>contato.php">
                        Contato
                    </a>

                <?php endif; ?>

            </nav>

            <div class="rc-desktop-actions">

                <?php if ($usuarioLogado): ?>

                    <span class="rc-usuario">

                        <?php if ($ehAdmin): ?>

                            Administrador

                        <?php else: ?>

                            Olá, <?= htmlspecialchars($primeiroNome) ?>

                        <?php endif; ?>

                    </span>

                    <a
                        href="<?= $prefixo ?>conta/sair.php"
                        class="rc-login"
                    >
                        Sair
                    </a>

                <?php else: ?>

                    <a
                        href="<?= $prefixo ?>conta/login.php"
                        class="rc-login"
                    >
                        Entrar
                    </a>

                <?php endif; ?>

                <?php if (!$ehAdmin): ?>

                    <a
                        href="<?= $prefixo ?>carrinho/"
                        class="rc-carrinho"
                        aria-label="Carrinho"
                    >
                        🛒

                        <span
                            class="rc-carrinho-contador <?= $quantidadeCarrinho === 0 ? "vazio" : "" ?>"
                        >
                            <?= $quantidadeCarrinho ?>
                        </span>
                    </a>

                <?php endif; ?>

            </div>

        </div>

        <details class="rc-mobile">

            <summary
                class="rc-mobile-botao"
                aria-label="Menu"
            >

                <svg
                    class="rc-icone-menu"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                >
                    <path d="M4 6h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 18h16"></path>
                </svg>

                <svg
                    class="rc-icone-fechar"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                >
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6L6 18"></path>
                </svg>

            </summary>

            <div class="rc-mobile-painel">

                <nav class="rc-mobile-nav">

                    <?php if ($ehAdmin): ?>

                        <a href="<?= $prefixo ?>admin/">
                            Painel
                        </a>

                        <a href="<?= $prefixo ?>admin/produtos/">
                            Produtos
                        </a>

                        <a href="<?= $prefixo ?>admin/pedidos/">
                            Pedidos
                        </a>

                        <a href="<?= $prefixo ?>contato.php">
                            Contato
                        </a>

                    <?php else: ?>

                        <a href="<?= $prefixo ?>index.php">
                            Início
                        </a>

                        <a href="<?= $prefixo ?>cardapio.php">
                            Cardápio
                        </a>

                        <?php if (
                            $usuarioLogado &&
                            $usuarioLogado["tipo"] === "cliente"
                        ): ?>

                            <a href="<?= $prefixo ?>pedidos/">
                                Meus pedidos
                            </a>

                        <?php endif; ?>

                        <a href="<?= $prefixo ?>contato.php">
                            Contato
                        </a>

                    <?php endif; ?>

                </nav>

                <div class="rc-mobile-acoes">

                    <?php if ($usuarioLogado): ?>

                        <span class="rc-usuario">

                            <?php if ($ehAdmin): ?>

                                Administrador

                            <?php else: ?>

                                Olá, <?= htmlspecialchars($primeiroNome) ?>

                            <?php endif; ?>

                        </span>

                        <a
                            href="<?= $prefixo ?>conta/sair.php"
                            class="rc-login"
                        >
                            Sair
                        </a>

                    <?php else: ?>

                        <a
                            href="<?= $prefixo ?>conta/login.php"
                            class="rc-login"
                        >
                            Entrar
                        </a>

                    <?php endif; ?>

                    <?php if (!$ehAdmin): ?>

                        <a
                            href="<?= $prefixo ?>carrinho/"
                            class="rc-carrinho"
                            aria-label="Carrinho"
                        >
                            🛒

                            <span
                                class="rc-carrinho-contador <?= $quantidadeCarrinho === 0 ? "vazio" : "" ?>"
                            >
                                <?= $quantidadeCarrinho ?>
                            </span>
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </details>

    </div>

</header>

<main>