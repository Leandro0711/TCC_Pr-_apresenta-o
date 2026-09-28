<?php
// PONTO ÚNICO DO LOGO.
// Emblema redondo (árvore no anel, recortado de imagemLogoNoSite.jpeg) + nome
// "HealthCore" em Fraunces. Para trocar o desenho, substitua
// assets/img/logo-healthcore.png; nenhuma página precisa mudar.
// O emblema é decorativo (alt vazio): o nome já está escrito ao lado.

// $comEmblema = false mostra só o nome (usado no topo da página inicial).
function logo($destaque = false, $comEmblema = true){
    $classe = $destaque ? "logo logo--destaque" : "logo";
    $tamanho = $destaque ? 64 : 32;
    $emblema = $comEmblema
        ? '<img class="logo-emblema" src="assets/img/logo-healthcore.png" alt="" width="' . $tamanho . '" height="' . $tamanho . '">'
        : '';
    echo '<span class="' . $classe . '">' . $emblema . '<span class="logo-nome">HealthCore</span></span>';
}
