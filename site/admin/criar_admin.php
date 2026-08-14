<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Configuração inicial | Rino Cream";
$prefixo = "../";

$erro = "";

$email = "";

$sqlVerificarAdmin = "
    SELECT idusuario
    FROM usuarios
    WHERE tipo = 'admin'
    LIMIT 1
";

$stmtVerificarAdmin =
    $pdo->prepare($sqlVerificarAdmin);

$stmtVerificarAdmin->execute();

$adminExistente =
    $stmtVerificarAdmin->fetch();

if ($adminExistente) {

    if (
        isset($_SESSION["usuario"]) &&
        $_SESSION["usuario"]["tipo"] === "admin"
    ) {

        header("Location: index.php");
        exit;
    }
}

if (
    !$adminExistente &&
    $_SERVER["REQUEST_METHOD"] === "POST"
) {

    $email =
        trim($_POST["email"] ?? "");

    $senha =
        $_POST["senha"] ?? "";

    $confirmarSenha =
        $_POST["confirmar_senha"] ?? "";

    if (
        $email === "" ||
        $senha === "" ||
        $confirmarSenha === ""
    ) {

        $erro =
            "Preencha todos os campos.";

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $erro =
            "Digite um e-mail válido.";

    } elseif (
        strlen($senha) < 6
    ) {

        $erro =
            "A senha deve possuir pelo menos 6 caracteres.";

    } elseif (
        $senha !== $confirmarSenha
    ) {

        $erro =
            "As senhas não coincidem.";

    } else {

        $sqlEmail = "
            SELECT idusuario
            FROM usuarios
            WHERE email = :email
        ";

        $stmtEmail =
            $pdo->prepare($sqlEmail);

        $stmtEmail->execute([
            ":email" => $email
        ]);

        if ($stmtEmail->fetch()) {

            $erro =
                "Esse e-mail já está cadastrado.";

        } else {

            try {

                $senhaHash =
                    password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    );

                $sqlAdmin = "
                    INSERT INTO usuarios
                    (
                        email,
                        senha,
                        tipo
                    )
                    VALUES
                    (
                        :email,
                        :senha,
                        'admin'
                    )
                ";

                $stmtAdmin =
                    $pdo->prepare($sqlAdmin);

                $stmtAdmin->execute([
                    ":email" => $email,
                    ":senha" => $senhaHash
                ]);

                $idusuario =
                    $pdo->lastInsertId();

                session_regenerate_id(true);

                $_SESSION["usuario"] = [
                    "idusuario" => $idusuario,
                    "idcliente" => null,
                    "nome" => "Administrador",
                    "email" => $email,
                    "tipo" => "admin"
                ];

                header("Location: index.php");
                exit;

            } catch (PDOException $e) {

                $erro =
                    "Não foi possível criar o administrador.";
            }
        }
    }
}

require_once "../includes/header.php";

?>

<section class="conta-pagina">

    <div class="conta-card conta-card-login">

        <div class="conta-cabecalho">

            <span>
                ⚙️ Rino Cream
            </span>

            <?php if ($adminExistente): ?>

                <h1>
                    Configuração concluída
                </h1>

                <p>
                    A conta administrativa deste sistema
                    já foi criada.
                </p>

            <?php else: ?>

                <h1>
                    Criar administrador
                </h1>

                <p>
                    Configure a primeira conta administrativa
                    da Rino Cream.
                </p>

            <?php endif; ?>

        </div>

        <?php if ($erro !== ""): ?>

            <div class="mensagem-erro">

                <?= htmlspecialchars($erro) ?>

            </div>

        <?php endif; ?>

        <?php if ($adminExistente): ?>

            <div class="conta-link">

                <a href="../conta/login.php">
                    Ir para o login
                </a>

            </div>

        <?php else: ?>

            <form
                method="POST"
                class="conta-formulario"
            >

                <div class="campo">

                    <label for="email">
                        E-mail do administrador
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        placeholder="admin@rinocream.com"
                        autocomplete="email"
                        required
                    >

                </div>

                <div class="campo">

                    <label for="senha">
                        Senha
                    </label>

                    <div class="campo-senha">

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Mínimo de 6 caracteres"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="botao-ver-senha"
                            data-senha="senha"
                            aria-label="Mostrar senha"
                            title="Mostrar senha"
                        >
                        </button>

                    </div>

                </div>

                <div class="campo">

                    <label for="confirmar_senha">
                        Confirmar senha
                    </label>

                    <div class="campo-senha">

                        <input
                            type="password"
                            id="confirmar_senha"
                            name="confirmar_senha"
                            placeholder="Digite a senha novamente"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="botao-ver-senha"
                            data-senha="confirmar_senha"
                            aria-label="Mostrar senha"
                            title="Mostrar senha"
                        >
                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="botao-conta"
                >
                    Criar administrador
                </button>

            </form>

        <?php endif; ?>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>