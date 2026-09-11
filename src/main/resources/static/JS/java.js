if (lettersContainer) {
    const alphabet = "ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
    alphabet.split("").forEach(letter => {
        const div = document.createElement("div");
        div.classList.add("letter");

        div.innerHTML = `
            <h3>${letter}</h3>
            <input type="file">
        `;

        lettersContainer.appendChild(div);
    });
}

// Barra de búsqueda simple
const searchBar = document.getElementById("searchBar");

if (searchBar) {
    searchBar.addEventListener("keyup", function() {
        alert("Buscando: " + searchBar.value);
    });
}
