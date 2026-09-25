document.addEventListener("DOMContentLoaded", function () {

    const carrosseis = document.querySelectorAll("[data-carrossel]");

    carrosseis.forEach(inicializarCarrossel);

    function inicializarCarrossel(raiz) {

        const trilho = raiz.querySelector("[data-carrossel-trilho]");
        const viewport = raiz.querySelector(".carrossel-viewport");
        const botaoAnterior = raiz.querySelector("[data-carrossel-anterior]");
        const botaoProximo = raiz.querySelector("[data-carrossel-proximo]");
        const secao = raiz.closest("section");
        const dotsWrap = secao
            ? secao.querySelector("[data-carrossel-dots]")
            : null;

        if (!trilho || !viewport) {
            return;
        }

        const cartoes = Array.from(trilho.children);

        if (cartoes.length === 0) {
            return;
        }

        let indice = 0;
        let porPagina = 1;
        let maximoIndice = 0;
        let autoplayTimer = null;
        let arrastando = false;
        let inicioX = 0;
        let deslocamentoInicial = 0;

        function calcularLayout() {

            const larguraCartao = cartoes[0].getBoundingClientRect().width;
            const estiloTrilho = getComputedStyle(trilho);
            const gap = parseFloat(estiloTrilho.columnGap || estiloTrilho.gap || 0) || 0;
            const larguraViewport = viewport.getBoundingClientRect().width;

            porPagina = Math.max(
                1,
                Math.round(larguraViewport / (larguraCartao + gap))
            );

            maximoIndice = Math.max(0, cartoes.length - porPagina);

            if (indice > maximoIndice) {
                indice = maximoIndice;
            }

            construirDots();
            aplicarPosicao(false);
        }

        function construirDots() {

            if (!dotsWrap) {
                return;
            }

            dotsWrap.innerHTML = "";

            if (maximoIndice <= 0) {
                dotsWrap.classList.add("escondido");
                return;
            }

            dotsWrap.classList.remove("escondido");

            for (let i = 0; i <= maximoIndice; i++) {

                const dot = document.createElement("button");
                dot.type = "button";
                dot.className = "carrossel-dot" + (i === indice ? " active" : "");
                dot.setAttribute("aria-label", "Ir para o item " + (i + 1));

                dot.addEventListener("click", function () {
                    pararAutoplay();
                    irPara(i);
                    iniciarAutoplay();
                });

                dotsWrap.appendChild(dot);
            }
        }

        function atualizarDots() {

            if (!dotsWrap) {
                return;
            }

            const dots = dotsWrap.querySelectorAll(".carrossel-dot");

            dots.forEach(function (dot, i) {
                dot.classList.toggle("active", i === indice);
            });
        }

        function aplicarPosicao(comTransicao) {

            const larguraCartao = cartoes[0].getBoundingClientRect().width;
            const estiloTrilho = getComputedStyle(trilho);
            const gap = parseFloat(estiloTrilho.columnGap || estiloTrilho.gap || 0) || 0;
            const passo = larguraCartao + gap;

            trilho.style.transition = comTransicao
                ? "transform 0.45s cubic-bezier(0.22, 0.61, 0.36, 1)"
                : "none";

            trilho.style.transform = "translateX(" + (-indice * passo) + "px)";

            atualizarDots();

            if (botaoAnterior) {
                botaoAnterior.classList.toggle("escondido", indice <= 0);
            }

            if (botaoProximo) {
                botaoProximo.classList.toggle("escondido", indice >= maximoIndice);
            }
        }

        function irPara(novoIndice) {

            indice = Math.max(0, Math.min(maximoIndice, novoIndice));
            aplicarPosicao(true);
        }

        function iniciarAutoplay() {

            pararAutoplay();

            if (maximoIndice <= 0) {
                return;
            }

            autoplayTimer = setInterval(function () {
                irPara(indice >= maximoIndice ? 0 : indice + 1);
            }, 4500);
        }

        function pararAutoplay() {

            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        if (botaoAnterior) {
            botaoAnterior.addEventListener("click", function () {
                pararAutoplay();
                irPara(indice - 1);
                iniciarAutoplay();
            });
        }

        if (botaoProximo) {
            botaoProximo.addEventListener("click", function () {
                pararAutoplay();
                irPara(indice + 1);
                iniciarAutoplay();
            });
        }

        // Arrastar com mouse/toque — fica restrito ao carrossel, nunca ao scroll da página
        viewport.addEventListener("pointerdown", function (e) {

            arrastando = true;
            inicioX = e.clientX;

            const larguraCartao = cartoes[0].getBoundingClientRect().width;
            const estiloTrilho = getComputedStyle(trilho);
            const gap = parseFloat(estiloTrilho.columnGap || estiloTrilho.gap || 0) || 0;

            deslocamentoInicial = -indice * (larguraCartao + gap);

            trilho.style.transition = "none";
            pararAutoplay();

            viewport.setPointerCapture(e.pointerId);
        });

        viewport.addEventListener("pointermove", function (e) {

            if (!arrastando) {
                return;
            }

            const delta = e.clientX - inicioX;
            trilho.style.transform = "translateX(" + (deslocamentoInicial + delta) + "px)";
        });

        function soltar(e) {

            if (!arrastando) {
                return;
            }

            arrastando = false;

            const delta = e.clientX - inicioX;
            const larguraCartao = cartoes[0].getBoundingClientRect().width;

            if (Math.abs(delta) > larguraCartao * 0.2) {
                irPara(delta < 0 ? indice + 1 : indice - 1);
            } else {
                aplicarPosicao(true);
            }

            iniciarAutoplay();
        }

        viewport.addEventListener("pointerup", soltar);
        viewport.addEventListener("pointercancel", soltar);
        viewport.addEventListener("pointerleave", function (e) {
            if (arrastando) {
                soltar(e);
            }
        });

        raiz.addEventListener("mouseenter", pararAutoplay);
        raiz.addEventListener("mouseleave", iniciarAutoplay);
        raiz.addEventListener("focusin", pararAutoplay);
        raiz.addEventListener("focusout", iniciarAutoplay);

        window.addEventListener("resize", calcularLayout);

        calcularLayout();
        iniciarAutoplay();
    }

});
