<?php
require_once 'Peticiones.php'; // Asegúrate de que la clase Peticiones esté disponible

// Inicializar las peticiones
$peticiones = new Peticiones();

// Obtener los filtros y el ordenamiento desde el formulario (o valores por defecto)
$order = isset($_GET['order']) ? $_GET['order'] : 'A-Z';
$species = isset($_GET['species']) ? $_GET['species'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$origin = isset($_GET['origin']) ? $_GET['origin'] : 'all';

// Obtener la lista de personajes de la API
$personajes = $peticiones->obtenerPersonajes(); // Obtener personajes (puedes agregar paginación si es necesario)

// Filtrar por especie, estado, tipo y origen
$personajesFiltrados = array_filter($personajes, function($personaje) use ($species, $status, $type, $origin) {
    $filtroEspecie = $species === 'all' || $personaje['species'] === $species;
    $filtroEstado = $status === 'all' || (isset($personaje['status']) && $personaje['status'] === $status);
    $filtroTipo = $type === 'all' || (isset($personaje['type']) && $personaje['type'] === $type);
    $filtroOrigen = $origin === 'all' || (isset($personaje['origin']['name']) && $personaje['origin']['name'] === $origin);
    return $filtroEspecie && $filtroEstado && $filtroTipo && $filtroOrigen;
});

// Ordenar personajes según el criterio
if ($order === 'A-Z') {
    usort($personajesFiltrados, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
} else {
    usort($personajesFiltrados, function($a, $b) {
        return strcmp($b['name'], $a['name']);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick and Morty Characters</title>
    <link rel="stylesheet" href="Styles/main.css">
    <script defer src="Scripts/Main.js"></script>
    <script defer src="Scripts/CardControl.js"></script>
</head>
<body>
    <header>
        <div class="banner">
            <h1>Rick and Morty Character Explorer</h1>
            <div class="filters">
                <form method="GET" id="filterForm">
                    <div class="filters">
                        <label for="sort">Order:</label>
                        <select id="sort" name="order" onchange="document.getElementById('filterForm').submit()">
                            <option value="A-Z" <?= $order === 'A-Z' ? 'selected' : '' ?>>A-Z</option>
                            <option value="Z-A" <?= $order === 'Z-A' ? 'selected' : '' ?>>Z-A</option>
                        </select>
                    </div>
                    <div class="filters">
                        <label for="species">Species:</label>
                        <select id="species" name="species" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?= $species === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Human" <?= $species === 'Human' ? 'selected' : '' ?>>Human</option>
                            <option value="Alien" <?= $species === 'Alien' ? 'selected' : '' ?>>Alien</option>
                        </select>
                    </div>
                    <div class="filters">
                        <label for="status">Status:</label>
                        <select id="status" name="status" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Alive" <?= $status === 'Alive' ? 'selected' : '' ?>>Alive</option>
                            <option value="Dead" <?= $status === 'Dead' ? 'selected' : '' ?>>Dead</option>
                        </select>
                    </div>
                    <div class="filters">
                        <label for="type">Type:</label>
                        <select id="type" name="type" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Humanoid" <?= $type === 'Humanoid' ? 'selected' : '' ?>>Humanoid</option>
                            <option value="Animal" <?= $type === 'Animal' ? 'selected' : '' ?>>Animal</option>
                        </select>
                    </div>
                    <div class="filters">
                        <label for="origin">Origin:</label>
                        <select id="origin" name="origin" onchange="document.getElementById('filterForm').submit()">
                            <option value="all" <?= $origin === 'all' ? 'selected' : '' ?>>All</option>
                            <option value="Earth (C-137)" <?= $origin === 'Earth (C-137)' ? 'selected' : '' ?>>Earth (C-137)</option>
                            <option value="Unknown" <?= $origin === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <main>
        <div id="characters-list" class="characters-list">
            <?php foreach ($personajesFiltrados as $personaje): ?>
                <?php 
                // Incluimos el archivo Tarjeta.php y le pasamos el objeto $personaje
                include 'Tarjeta.php'; 
                ?>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>Created by Jhonattan Garcia, using the Rick and Morty API</p>
    </footer>
</body>
</html>
