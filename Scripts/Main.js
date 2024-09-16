// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    const sortSelect = document.getElementById('sort');
    const speciesSelect = document.getElementById('species');
    const statusSelect = document.getElementById('status');
    const locationSelect = document.getElementById('location');
    const charactersList = document.getElementById('characters-list');

    let characters = []; // Aquí se almacenarán los personajes traídos de la API
    let favorites = new Set(); // Para manejar los favoritos

    // Función para obtener los personajes de la API
    const fetchCharacters = async () => {
        try {
            const response = await fetch('Peticiones.php?endpoint=characters');
            const data = await response.json();
            characters = data.characters.results;
            renderCharacters(characters);
        } catch (error) {
            console.error('Error fetching characters:', error);
        }
    };

    // Función para renderizar los personajes en la lista
    const renderCharacters = (characterData) => {
        charactersList.innerHTML = ''; // Limpia la lista
        characterData.forEach(character => {
            const characterItem = document.createElement('div');
            characterItem.classList.add('character-item');

            characterItem.innerHTML = `
                <div class="character-header" data-id="${character.id}">
                    <h2>#${character.id} ${character.name} 
                    <span class="favorite ${favorites.has(character.id) ? 'favorite-active' : ''}">✰</span></h2>
                </div>
                <div class="character-details" id="details-${character.id}" style="display: none;">
                    <div class="character-card">
                        <img src="${character.image}" alt="${character.name}">
                        <p><strong>Species:</strong> ${character.species}</p>
                        <p><strong>Status:</strong> ${character.status}</p>
                    </div>
                </div>
            `;

            // Añade funcionalidad de expandir/contraer detalles
            const header = characterItem.querySelector('.character-header');
            header.addEventListener('click', () => {
                const details = document.getElementById(`details-${character.id}`);
                details.style.display = details.style.display === 'none' ? 'block' : 'none';
            });

            // Añade funcionalidad de favoritos
            const favoriteIcon = header.querySelector('.favorite');
            favoriteIcon.addEventListener('click', (e) => {
                e.stopPropagation(); // Evita que el clic también expanda/contraiga
                toggleFavorite(character.id, favoriteIcon);
            });

            charactersList.appendChild(characterItem);
        });
    };

    // Función para manejar favoritos
    const toggleFavorite = (id, icon) => {
        if (favorites.has(id)) {
            favorites.delete(id);
            icon.classList.remove('favorite-active');
        } else {
            favorites.add(id);
            icon.classList.add('favorite-active');
        }
        saveFavorites();
    };

    // Guardar favoritos en localStorage
    const saveFavorites = () => {
        localStorage.setItem('favorites', JSON.stringify([...favorites]));
    };

    // Cargar favoritos desde localStorage
    const loadFavorites = () => {
        const storedFavorites = JSON.parse(localStorage.getItem('favorites')) || [];
        favorites = new Set(storedFavorites);
    };

    // Función para aplicar filtros en tiempo real
    const applyFilters = () => {
        let filteredCharacters = characters;

        const speciesValue = speciesSelect.value;
        if (speciesValue !== 'all') {
            filteredCharacters = filteredCharacters.filter(c => c.species === speciesValue);
        }

        const statusValue = statusSelect.value;
        if (statusValue !== 'all') {
            filteredCharacters = filteredCharacters.filter(c => c.status === statusValue);
        }

        renderCharacters(filteredCharacters);
    };

    // Función para ordenar personajes
    const sortCharacters = (order) => {
        const sortedCharacters = [...characters].sort((a, b) => {
            if (order === 'A-Z') {
                return a.name.localeCompare(b.name);
            } else {
                return b.name.localeCompare(a.name);
            }
        });
        renderCharacters(sortedCharacters);
    };

    // Event listeners para los filtros
    sortSelect.addEventListener('change', () => sortCharacters(sortSelect.value));
    speciesSelect.addEventListener('change', applyFilters);
    statusSelect.addEventListener('change', applyFilters);
    locationSelect.addEventListener('change', applyFilters);

    // Inicialización
    loadFavorites();
    fetchCharacters();
});
