<?php

session_start();

require_once "../config/conexao.php";

$titulo = "Criar conta | Rino Cream";
$prefixo = "../";

$erro = "";

$nome = "";
$email = "";
$telefone = "";
$endereco = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $endereco = trim($_POST["endereco"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    if (
        $nome === "" ||
        $email === "" ||
        $telefone === "" ||
        $endereco === "" ||
        $senha === "" ||
        $confirmarSenha === ""
    ) {

        $erro = "Preencha todos os campos.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $erro = "Digite um e-mail válido.";

    } elseif (strlen($senha) < 6) {

        $erro = "A senha deve possuir pelo menos 6 caracteres.";

    } elseif ($senha !== $confirmarSenha) {

        $erro = "As senhas não coincidem.";

    } else {

        $sqlVerificar = "
            SELECT idusuario
            FROM usuarios
            WHERE email = :email
        ";

        $stmtVerificar = $pdo->prepare(
            $sqlVerificar
        );

        $stmtVerificar->execute([
            ":email" => $email
        ]);

        if ($stmtVerificar->fetch()) {

            $erro =
                "Já existe uma conta cadastrada com este e-mail.";

        } else {

            try {

                $pdo->beginTransaction();

                $senhaHash = password_hash(
                    $senha,
                    PASSWORD_DEFAULT
                );

                $sqlUsuario = "
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
                        'cliente'
                    )
                ";

                $stmtUsuario =
                    $pdo->prepare(
                        $sqlUsuario
                    );

                $stmtUsuario->execute([
                    ":email" => $email,
                    ":senha" => $senhaHash
                ]);

                $idusuario =
                    $pdo->lastInsertId();

                $sqlCliente = "
                    INSERT INTO clientes
                    (
                        idusuario,
                        nome,
                        telefone,
                        endereco
                    )
                    VALUES
                    (
                        :idusuario,
                        :nome,
                        :telefone,
                        :endereco
                    )
                ";

                $stmtCliente =
                    $pdo->prepare(
                        $sqlCliente
                    );

                $stmtCliente->execute([
                    ":idusuario" => $idusuario,
                    ":nome" => $nome,
                    ":telefone" => $telefone,
                    ":endereco" => $endereco
                ]);

                $idcliente =
                    $pdo->lastInsertId();

                $pdo->commit();

                session_regenerate_id(true);

                $_SESSION["usuario"] = [
                    "idusuario" => $idusuario,
                    "idcliente" => $idcliente,
                    "nome" => $nome,
                    "email" => $email,
                    "tipo" => "cliente"
                ];

                header(
                    "Location: ../index.php"
                );

                exit;

            } catch (PDOException $e) {

                if ($pdo->inTransaction()) {

                    $pdo->rollBack();
                }

                $erro =
                    "Não foi possível criar a conta. Tente novamente.";
            }
        }
    }
}

require_once "../includes/header.php";

?>

<section class="conta-pagina">

    <div class="conta-card">

        <div class="conta-cabecalho">

            <span>
                🍦 Bem-vindo à Rino Cream
            </span>

            <h1>
                Criar conta
            </h1>

            <p>
                Cadastre-se para montar seus pedidos e acompanhar suas compras.
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

                <label for="nome">
                    Nome
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars($nome) ?>"
                    placeholder="Digite seu nome"
                    autocomplete="name"
                    required
                >

            </div>

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

                <label for="telefone">
                    Telefone
                </label>

                <input
                    type="text"
                    id="telefone"
                    name="telefone"
                    value="<?= htmlspecialchars($telefone) ?>"
                    placeholder="(11) 99999-9999"
                    autocomplete="tel"
                    required
                >

            </div>

            <div class="campo">

                <label for="endereco">
                    Endereço
                </label>

                <input
                    type="text"
                    id="endereco"
                    name="endereco"
                    value="<?= htmlspecialchars($endereco) ?>"
                    placeholder="Rua, número e bairro"
                    autocomplete="street-address"
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
                Criar conta
            </button>

        </form>

        <div class="conta-link">

            <p>
                Já possui uma conta?

                <a href="login.php">
                    Entrar
                </a>

            </p>

        </div>

    </div>

</section>

<?php

require_once "../includes/footer.php";

?>