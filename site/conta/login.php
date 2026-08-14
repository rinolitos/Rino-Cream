<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Entrar | Rino Cream";
$prefixo = "../";

$erro = "";
$email = "";

if (isset($_SESSION["usuario"])) {

    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($email === "" || $senha === "") {

        $erro = "Preencha o e-mail e a senha.";

    } else {

        $sql = "
            SELECT
                usuarios.idusuario,
                usuarios.email,
                usuarios.senha,
                usuarios.tipo,
                clientes.idcliente,
                clientes.nome
            FROM usuarios
            LEFT JOIN clientes
                ON clientes.idusuario = usuarios.idusuario
            WHERE usuarios.email = :email
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":email" => $email
        ]);

        $usuario = $stmt->fetch();

        if (
            $usuario &&
            password_verify(
                $senha,
                $usuario["senha"]
            )
        ) {

            session_regenerate_id(true);

            $nomeUsuario = $usuario["nome"];

            if (!$nomeUsuario) {
                $nomeUsuario = "Administrador";
            }

            $_SESSION["usuario"] = [
                "idusuario" => $usuario["idusuario"],
                "idcliente" => $usuario["idcliente"],
                "nome" => $nomeUsuario,
                "email" => $usuario["email"],
                "tipo" => $usuario["tipo"]
            ];

            $destino =
                $_SESSION["destino_apos_login"]
                ?? "../index.php";

            unset(
                $_SESSION["destino_apos_login"]
            );

            header(
                "Location: " . $destino
            );

            exit;

        } else {

            $erro =
                "E-mail ou senha incorretos.";
        }
    }
}

require_once "../includes/header.php";

?>

<section class="conta-pagina">

    <div class="conta-card conta-card-login">

        <div class="conta-cabecalho">

            <span>
                🍨 Que bom ter você aqui
            </span>

            <h1>
                Entrar
            </h1>

            <p>
                Acesse sua conta para continuar seu pedido.
            </p>

        </div>

        <?php if ($erro !== ""): ?>

            <div class="mensagem-erro">
                <?= htmlspecialchars($erro) ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            class="conta-formulario"
        >

            <div class="campo">

                <label for="email">
                    E-mail
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="seuemail@exemplo.com"
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
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
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

            <button
                type="submit"
                class="botao-conta"
            >
                Entrar
            </button>

        </form>

        <div class="conta-link">

            <p>
                Ainda não possui uma conta?

                <a href="cadastro.php">
                    Criar cadastro
                </a>

            </p>

        </div>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>