const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
const body = document.body;
const htmlElement = document.documentElement;

if (!htmlElement.hasAttribute('data-bs-theme')) {
  htmlElement.setAttribute('data-bs-theme', 'auto');
}

function handleThemeChange(e) {
    if (e.matches) {
        console.log("Changement vers thème sombre");
        body.dataset.bsTheme = "dark";
        var image = document.getElementById("logo-img");
        image.src = image.src.replace("logo-d.png", "logo-l.png")
    } else {
        console.log("Changement vers thème clair");
        body.dataset.bsTheme = "light";
        var image = document.getElementById("logo-img");
        image.src = image.src.replace("logo-l.png", "logo-d.png")
    }
}

mediaQuery.addEventListener('change', handleThemeChange);
handleThemeChange(mediaQuery);