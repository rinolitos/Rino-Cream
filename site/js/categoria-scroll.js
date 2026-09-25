document.addEventListener("DOMContentLoaded", function () {

    const secao = document.querySelector("[data-cs-secao]");

    if (!secao) {
        return;
    }

    const wrapper = secao.querySelector("[data-cs-wrapper]");
    const slides = secao.querySelectorAll(".cs-slide");
    const dots = secao.querySelectorAll(".cs-dot");
    const dotsWrap = secao.querySelector("[data-cs-dots]");
    const setaEsq = secao.querySelector("[data-cs-seta-esq]");
    const setaDir = secao.querySelector("[data-cs-seta-dir]");
    const dica = secao.querySelector("[data-cs-dica]");
    const continuar = secao.querySelector("[data-cs-continuar]");

    const total = slides.length;

    if (total <= 1) {
        // Só um produto: mostra estático, sem sequestrar o scroll da página
        return;
    }

    let atual = 0;
    let animando = false;
    let jaEntrouNoConteudo = false;

    function irPara(indice) {

        if (indice < 0 || indice >= total || indice === atual || animando) {
            return;
        }

        animando = true;

        slides[atual].classList.remove("active");
        if (dots[atual]) {
            dots[atual].classList.remove("active");
        }

        atual = indice;

        wrapper.style.transform = "translateX(-" + (atual * 100) + "%)";

        slides[atual].classList.add("active");
        if (dots[atual]) {
            dots[atual].classList.add("active");
        }

        if (setaEsq) {
            setaEsq.classList.toggle("escondido", atual === 0);
        }

        if (setaDir) {
            setaDir.classList.toggle("escondido", atual === total - 1);
        }

        if (atual > 0 && dica) {
            dica.classList.add("escondido");
        }

        setTimeout(function () {
            animando = false;
        }, 900);
    }

    function estaNaSecao() {

        const retangulo = secao.getBoundingClientRect();

        // Considera "dentro da vitrine" enquanto o topo da seção
        // ainda está visível logo abaixo do cabeçalho fixo
        return retangulo.top > -10 && retangulo.top < window.innerHeight * 0.6;
    }

    function atualizarVisibilidadeChrome() {

        const dentro = estaNaSecao();

        if (dotsWrap) {
            dotsWrap.classList.toggle("escondido", !dentro);
        }

        if (setaEsq) {
            setaEsq.classList.toggle("escondido", !dentro || atual === 0);
        }

        if (setaDir) {
            setaDir.classList.toggle("escondido", !dentro || atual === total - 1);
        }

        if (dica) {
            dica.classList.toggle("escondido", !dentro || atual > 0);
        }
    }

    window.addEventListener("scroll", atualizarVisibilidadeChrome, { passive: true });

    window.addEventListener("scroll", function () {
        if (!estaNaSecao()) {
            jaEntrouNoConteudo = false;
        }
    }, { passive: true });

    let esperandoRoda = false;

    window.addEventListener("wheel", function (e) {

        if (!estaNaSecao()) {
            return;
        }

        const descendo = e.deltaY > 30 || e.deltaX > 30;
        const subindo = e.deltaY < -30 || e.deltaX < -30;

        if (!descendo && !subindo) {
            return;
        }

        if (atual === total - 1 && descendo) {

            if (!jaEntrouNoConteudo && continuar) {
                jaEntrouNoConteudo = true;
                e.preventDefault();
                continuar.scrollIntoView({ behavior: "smooth", block: "start" });
            }

            return;
        }

        if (atual === 0 && subindo) {
            // Deixa rolar normalmente para cima, saindo da vitrine
            return;
        }

        e.preventDefault();

        if (esperandoRoda) {
            return;
        }

        esperandoRoda = true;

        if (descendo) {
            irPara(atual + 1);
        } else {
            irPara(atual - 1);
        }

        setTimeout(function () {
            esperandoRoda = false;
        }, 900);

    }, { passive: false });

    window.addEventListener("keydown", function (e) {

        if (!estaNaSecao()) {
            return;
        }

        if (e.key === "ArrowRight" || e.key === "ArrowDown") {
            irPara(atual + 1);
        }

        if (e.key === "ArrowLeft" || e.key === "ArrowUp") {
            irPara(atual - 1);
        }
    });

    let toqueX = 0;
    let toqueY = 0;

    secao.addEventListener("touchstart", function (e) {
        toqueX = e.touches[0].clientX;
        toqueY = e.touches[0].clientY;
    }, { passive: true });

    secao.addEventListener("touchmove", function (e) {

        if (!estaNaSecao()) {
            return;
        }

        const dy = e.touches[0].clientY - toqueY;
        const arrastandoParaBaixo = dy < 0;

        if (atual === total - 1 && arrastandoParaBaixo) {
            return;
        }

        e.preventDefault();

    }, { passive: false });

    secao.addEventListener("touchend", function (e) {

        if (!estaNaSecao()) {
            return;
        }

        const dx = toqueX - e.changedTouches[0].clientX;

        if (Math.abs(dx) > 50) {
            irPara(dx > 0 ? atual + 1 : atual - 1);
        }

    }, { passive: true });

    dots.forEach(function (dot) {
        dot.addEventListener("click", function () {
            irPara(Number(dot.dataset.target));
        });
    });

    if (setaEsq) {
        setaEsq.addEventListener("click", function () {
            irPara(atual - 1);
        });
    }

    if (setaDir) {
        setaDir.addEventListener("click", function () {
            irPara(atual + 1);
        });
    }

    atualizarVisibilidadeChrome();

});
