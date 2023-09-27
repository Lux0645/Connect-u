const themeToggle = document.getElementById("theme-toggle");
const content = document.getElementById("content");

themeToggle.addEventListener("click", () => {
    // Bascule entre le thème clair et sombre
    document.body.classList.toggle("dark-theme");
    // Vous pouvez également mettre à jour d'autres éléments de l'interface utilisateur ici

    // Stocke le thème actuel dans localStorage
    if (document.body.classList.contains("dark-theme")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
});

// Vérifie le thème stocké dans localStorage au chargement de la page
window.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");
    }
});
