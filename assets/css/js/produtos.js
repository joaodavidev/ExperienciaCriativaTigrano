const btnNovo = document.getElementById("btnNovoProduto");
const form = document.getElementById("formProduto");
const lista = document.getElementById("produtos");

btnNovo.addEventListener("click", () => {
  form.style.display = form.style.display === "flex" ? "none" : "flex";
});

form.addEventListener("submit", (e) => {
  e.preventDefault();

  const nome = document.getElementById("nome").value;
  const preco = document.getElementById("preco").value;
  const descricao = document.getElementById("descricao").value;

  const card = document.createElement("div");
  card.className = "produto";
  card.innerHTML = `
    <strong>${nome}</strong>
    <p>Preço: R$ ${parseFloat(preco).toFixed(2)}</p>
    <p>${descricao}</p>
  `;

  lista.appendChild(card);

  form.reset();
  form.style.display = "none";
});
