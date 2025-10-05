<?php include "config.php" ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>BioSpace Menu</title>
</head>
<body>
    <header> 
      <div id="casinha">  <svg id="home" xmlns="http://www.w3.org/2000/svg" fill="white"      viewBox="0 0 24 24" width="35" height="50">
        <path d="M12 3l9 8h-3v9h-12v-9h-3z"/></svg>
      </div>
      <div id="placeholder_form">
        <form method="post"> 
          <input type="text" placeholder="🔎 Pesquisar">
        </form>
      </div>
      <div id="title_principal">
        <h1 id="titulo-principal">BioSpace</h1>
      </div>  
    </header>

  <section id="menu">
    <div id="categorias">
      <h2>Categorias</h2>
    </div>

    <div id="submenu-lateral">
      <!-- Submenu aparece aqui -->
    </div>

    <div id="conteudo">
      <!-- Fundo inicial -->
    </div>
  </section>

  <script>
    const categorias = [
      {
        nome: "Humano",
        subitens: [
          { titulo: "Sobre Humanos", url: "https://pt.wikipedia.org/wiki/Corpo_humano" },
          { titulo: "Pesquisas", url: "https://www.nasa.gov" }
        ]
      },
      {
        nome: "Plantas",
        subitens: [
          { titulo: "Sobre Plantas", url: "https://pt.wikipedia.org/wiki/Plantae" },
          { titulo: "Pesquisas", url: "https://pmc.ncbi.nlm.nih.gov/" },
          { titulo: "Outra Pesquisa", url: "https://www.google.com" }
        ]
      }
    ];

    const categoriasDiv = document.getElementById("categorias");
    const submenuLateral = document.getElementById("submenu-lateral");
    const conteudoDiv = document.getElementById("conteudo");
    const tituloPrincipal = document.getElementById("titulo-principal");
    const home = document.getElementById("home")

    function abrirIframe(url) {
      submenuLateral.classList.remove("active");
      submenuLateral.innerHTML = "";

      conteudoDiv.classList.add("sem-fundo");
      conteudoDiv.innerHTML = `<iframe src="${url}"></iframe>`;

      const iframe = conteudoDiv.querySelector("iframe");
      iframe.addEventListener("load", () => {
        iframe.classList.add("loaded");
      });
    }

    categorias.forEach(cat => {
      const btn = document.createElement("button");
      btn.classList.add("rider");
      btn.textContent = cat.nome;

      btn.addEventListener("click", () => {
        submenuLateral.classList.add("active");
        submenuLateral.innerHTML = `<h3>${cat.nome}</h3>`;

        cat.subitens.forEach(sub => {
          const subBtn = document.createElement("button");
          subBtn.textContent = sub.titulo;
          subBtn.addEventListener("click", () => abrirIframe(sub.url));
          submenuLateral.appendChild(subBtn);
        });
      });

      categoriasDiv.appendChild(btn);
    });

    // Resetar para o fundo inicial ao clicar no título
    tituloPrincipal.addEventListener("click", () => {
      submenuLateral.classList.remove("active");
      submenuLateral.innerHTML = "";
      conteudoDiv.classList.remove("sem-fundo");
      conteudoDiv.innerHTML = "";
    });
        // Resetar para o fundo inicial ao clicar no título
    home.addEventListener("click", () => {
      submenuLateral.classList.remove("active");
      submenuLateral.innerHTML = "";
      conteudoDiv.classList.remove("sem-fundo");
      conteudoDiv.innerHTML = "";
    });
  </script>
</body>
</html>
