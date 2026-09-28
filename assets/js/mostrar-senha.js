// Único JavaScript do projeto: botão "Mostrar/Ocultar" do campo de senha.
// O botão vem com "hidden" no HTML e só aparece se este script rodar,
// então sem JS o campo continua funcionando normalmente, só sem o botão.
document.querySelectorAll("[data-mostrar-senha]").forEach(function (botao) {
  var campo = document.getElementById(botao.dataset.mostrarSenha);
  botao.hidden = false;
  botao.addEventListener("click", function () {
    var mostrar = campo.type === "password";
    campo.type = mostrar ? "text" : "password";
    botao.setAttribute("aria-pressed", String(mostrar));
    botao.textContent = mostrar ? "Ocultar" : "Mostrar";
  });
});
