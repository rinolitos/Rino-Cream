<?php

$arquivoScript =
    __DIR__ . "/../js/script.js";

$versaoScript =
    file_exists($arquivoScript)
        ? filemtime($arquivoScript)
        : 1;

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
    src="<?= $prefixo ?? "" ?>js/script.js?v=<?= $versaoScript ?>"
></script>

</body>

</html>