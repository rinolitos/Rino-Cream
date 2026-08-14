document.addEventListener("DOMContentLoaded", function () {

    const formularios = document.querySelectorAll(
        ".form-adicionar-carrinho"
    );

    formularios.forEach(function (formulario) {

        formulario.addEventListener(
            "submit",
            async function (event) {

                event.preventDefault();

                const botao =
                    formulario.querySelector("button");

                const textoOriginal =
                    botao.textContent;

                botao.disabled = true;

                try {

                    const dados =
                        new FormData(formulario);

                    dados.set("ajax", "1");

                    const resposta =
                        await fetch(
                            formulario.action,
                            {
                                method: "POST",
                                body: dados,
                                credentials: "same-origin",

                                headers: {
                                    "Accept":
                                        "application/json"
                                }
                            }
                        );

                    if (!resposta.ok) {

                        throw new Error(
                            "Erro na resposta do servidor."
                        );
                    }

                    const resultado =
                        await resposta.json();

                    if (!resultado.sucesso) {

                        if (
                            typeof resultado.quantidade !==
                            "undefined"
                        ) {

                            atualizarContadorCarrinho(
                                Number(
                                    resultado.quantidade
                                )
                            );
                        }

                        if (
                            resultado.mensagem ===
                            "Limite do estoque"
                        ) {

                            mostrarNotificacao(
                                "Você já adicionou todas as unidades disponíveis."
                            );

                        } else if (
                            resultado.mensagem ===
                            "Esgotado"
                        ) {

                            mostrarNotificacao(
                                "Este produto está esgotado."
                            );

                        } else {

                            mostrarNotificacao(
                                resultado.mensagem ||
                                "Produto indisponível."
                            );
                        }

                        botao.disabled = false;

                        return;
                    }

                    atualizarContadorCarrinho(
                        Number(resultado.quantidade)
                    );

                    botao.textContent = "✓";

                    botao.classList.add(
                        "adicionado"
                    );

                    setTimeout(function () {

                        botao.textContent =
                            textoOriginal;

                        botao.classList.remove(
                            "adicionado"
                        );

                        botao.disabled = false;

                    }, 700);

                } catch (erro) {

                    botao.disabled = false;

                    mostrarNotificacao(
                        "Não foi possível adicionar o produto. Tente novamente."
                    );
                }

            }
        );

    });


    function atualizarContadorCarrinho(
        quantidade
    ) {

        const contadores =
            document.querySelectorAll(
                ".rc-carrinho-contador"
            );

        contadores.forEach(
            function (contador) {

                contador.textContent =
                    quantidade;

                if (quantidade > 0) {

                    contador.classList.remove(
                        "vazio"
                    );

                } else {

                    contador.classList.add(
                        "vazio"
                    );
                }

            }
        );

    }


    function mostrarNotificacao(mensagem) {

        const notificacaoAntiga =
            document.querySelector(
                ".notificacao-estoque"
            );

        if (notificacaoAntiga) {

            notificacaoAntiga.remove();
        }

        const notificacao =
            document.createElement("div");

        notificacao.className =
            "notificacao-estoque";

        notificacao.innerHTML = `
            <span class="notificacao-estoque-icone">
                !
            </span>

            <span class="notificacao-estoque-texto">
                ${mensagem}
            </span>
        `;

        document.body.appendChild(
            notificacao
        );

        requestAnimationFrame(
            function () {

                notificacao.classList.add(
                    "mostrar"
                );

            }
        );

        setTimeout(function () {

            notificacao.classList.remove(
                "mostrar"
            );

            setTimeout(function () {

                if (
                    document.body.contains(
                        notificacao
                    )
                ) {

                    notificacao.remove();
                }

            }, 300);

        }, 2300);

    }


    const olhoAberto = `
        <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    `;


    const olhoFechado = `
        <svg
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="M3 10c2.5 3 5.5 4.5 9 4.5S18.5 13 21 10"></path>
            <path d="M6 14l-1.5 2"></path>
            <path d="M12 14.5V17"></path>
            <path d="M18 14l1.5 2"></path>
        </svg>
    `;


    const botoesSenha =
        document.querySelectorAll(
            ".botao-ver-senha"
        );

    botoesSenha.forEach(function (botao) {

        botao.innerHTML = olhoAberto;

        botao.addEventListener(
            "click",
            function () {

                const idCampo =
                    botao.dataset.senha;

                const campoSenha =
                    document.getElementById(
                        idCampo
                    );

                if (!campoSenha) {
                    return;
                }

                if (
                    campoSenha.type ===
                    "password"
                ) {

                    campoSenha.type = "text";

                    botao.innerHTML =
                        olhoFechado;

                    botao.setAttribute(
                        "aria-label",
                        "Ocultar senha"
                    );

                    botao.setAttribute(
                        "title",
                        "Ocultar senha"
                    );

                } else {

                    campoSenha.type =
                        "password";

                    botao.innerHTML =
                        olhoAberto;

                    botao.setAttribute(
                        "aria-label",
                        "Mostrar senha"
                    );

                    botao.setAttribute(
                        "title",
                        "Mostrar senha"
                    );

                }

            }
        );

    });

});