<?php

function rc_versao_arquivo($caminhoRelativo) {

    $caminhoCompleto =
        __DIR__ . "/../" . $caminhoRelativo;

    return file_exists($caminhoCompleto)
        ? filemtime($caminhoCompleto)
        : 1;
}

?>

</main>

<footer>

    <p>
        &copy;
        <?= date("Y") ?>
        Rino Cream.
        Todos os direitos reservados.
    </p>

</footer>

<script
    src="<?= $prefixo ?? "" ?>js/script.js?v=<?= rc_versao_arquivo("js/script.js") ?>"
></script>

<script
    src="<?= $prefixo ?? "" ?>js/categoria-scroll.js?v=<?= rc_versao_arquivo("js/categoria-scroll.js") ?>"
></script>

</body>

</html>