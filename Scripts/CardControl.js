console.log("Control de tarjetas.js cargado")
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de expandir/contraer detalles
    const headers = document.querySelectorAll('.character-header');
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const characterId = this.getAttribute('data-id');
            const details = document.getElementById(`details-${characterId}`);
            details.style.display = details.style.display === 'none' ? 'block' : 'none';
        });
    });

    // Funcionalidad de favoritos
    const favoriteIcons = document.querySelectorAll('.favorite');
    favoriteIcons.forEach(icon => {
        const characterId = icon.getAttribute('data-id');
        let favorites = new Set(JSON.parse(localStorage.getItem('favorites')) || []);

        // Inicializar favoritos
        if (favorites.has(parseInt(characterId))) {
            icon.classList.add('favorite-active');
        }

        // Añadir funcionalidad de favoritos
        icon.addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que el clic también expanda/contraiga
            toggleFavorite(characterId, icon);
        });
    });

    // Función para manejar favoritos
    function toggleFavorite(id, icon) {
        let favorites = new Set(JSON.parse(localStorage.getItem('favorites')) || []);

        if (favorites.has(parseInt(id))) {
            favorites.delete(parseInt(id));
            icon.classList.remove('favorite-active');
        } else {
            favorites.add(parseInt(id));
            icon.classList.add('favorite-active');
        }

        localStorage.setItem('favorites', JSON.stringify([...favorites]));
    }
});
