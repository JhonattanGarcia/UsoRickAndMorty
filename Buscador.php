<?php
// Función para cargar y ejecutar el archivo SQL local
function importarSQL($pdo, $sqlFile) {
    try {
        // Leer el contenido del archivo SQL
        $sqlContent = file_get_contents($sqlFile);

        // Dividimos el archivo SQL en consultas separadas usando ";"
        $queries = explode(';', $sqlContent);

        // Ejecutamos cada consulta
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
    } catch (PDOException $e) {
        die("Error al importar el archivo SQL: " . $e->getMessage());
    }
}

// Conectar a la base de datos usando PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bd_rick_morty', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Importar el archivo SQL si es necesario
    importarSQL($pdo, 'BackEnd/bd_rick_morty.sql'); // Ruta hacia el archivo SQL
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// Obtener los parámetros de búsqueda desde el formulario
$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$especie = isset($_GET['especie']) ? $_GET['especie'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$genero = isset($_GET['genero']) ? $_GET['genero'] : 'all';
$origen = isset($_GET['origen']) ? $_GET['origen'] : 'all';

// Crear la consulta SQL dinámica con los filtros
$sql = "SELECT personajes.id, personajes.nombre, personajes.especie, personajes.status, personajes.genero, origenes.nombre AS origen, episodios.titulo AS episodio, episodios.numero AS num_episodio, lugares.nombre AS lugar
        FROM personajes
        LEFT JOIN origenes ON personajes.origen_id = origenes.id
        LEFT JOIN episodios ON personajes.episodio_id = episodios.id
        LEFT JOIN lugares ON personajes.lugar_id = lugares.id
        WHERE 1=1";

// Agregar los filtros dinámicamente
if (!empty($nombre)) {
    $sql .= " AND personajes.nombre LIKE :nombre";
}
if ($especie !== 'all') {
    $sql .= " AND personajes.especie = :especie";
}
if ($status !== 'all') {
    $sql .= " AND personajes.status = :status";
}
if ($genero !== 'all') {
    $sql .= " AND personajes.genero = :genero";
}
if (!empty($origen)) {
    $sql .= " AND origenes.nombre LIKE :origen";
}

// Preparar la consulta
$stmt = $pdo->prepare($sql);

// Vincular los parámetros
if (!empty($nombre)) {
    $stmt->bindValue(':nombre', '%' . $nombre . '%');
}
if ($especie !== 'all') {
    $stmt->bindValue(':especie', $especie);
}
if ($status !== 'all') {
    $stmt->bindValue(':status', $status);
}
if ($genero !== 'all') {
    $stmt->bindValue(':genero', $genero);
}
if (!empty($origen)) {
    $stmt->bindValue(':origen', '%' . $origen . '%');
}

// Ejecutar la consulta
$stmt->execute();
$personajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rick and Morty Character Search</title>
    <link rel="stylesheet" href="Styles/main.css">
</head>
<body>
    <header>
        <h1>Buscador de Personajes de Rick y Morty</h1>
    </header>

    <main>
        <form method="GET" action="Buscador.php">
            <div class="filters">
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= $nombre ?>">
                </div>
                <div>
                    <label for="especie">Especie:</label>
                    <select id="especie" name="especie">
                        <option value="all" <?= $especie === 'all' ? 'selected' : '' ?>>Todas</option>
                        <option value="Human" <?= $especie === 'Human' ? 'selected' : '' ?>>Humano</option>
                        <option value="Alien" <?= $especie === 'Alien' ? 'selected' : '' ?>>Alien</option>
                    </select>
                </div>
                <div>
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Todos</option>
                        <option value="Alive" <?= $status === 'Alive' ? 'selected' : '' ?>>Vivo</option>
                        <option value="Dead" <?= $status === 'Dead' ? 'selected' : '' ?>>Muerto</option>
                    </select>
                </div>
                <div>
                    <label for="genero">Género:</label>
                    <select id="genero" name="genero">
                        <option value="all" <?= $genero === 'all' ? 'selected' : '' ?>>Todos</option>
                        <option value="Male" <?= $genero === 'Male' ? 'selected' : '' ?>>Masculino</option>
                        <option value="Female" <?= $genero === 'Female' ? 'selected' : '' ?>>Femenino</option>
                    </select>
                </div>
                <div>
                    <label for="origen">Origen:</label>
                    <input type="text" id="origen" name="origen" value="<?= $origen ?>">
                </div>
                <button type="submit">Buscar</button>
            </div>
        </form>

        <!-- Mostrar resultados -->
        <?php if (!empty($personajes)) : ?>
            <div class="results">
                <?php foreach ($personajes as $personaje) : ?>
                    <div class="character-item">
                        <h3>#<?= $personaje['id'] ?> - <?= $personaje['nombre'] ?></h3>
                        <p><strong>Especie:</strong> <?= $personaje['especie'] ?></p>
                        <p><strong>Status:</strong> <?= $personaje['status'] ?></p>
                        <p><strong>Género:</strong> <?= $personaje['genero'] ?></p>
                        <p><strong>Origen:</strong> <?= $personaje['origen'] ?></p>
                        <p><strong>Episodio:</strong> <?= $personaje['episodio'] ?> (Episodio #<?= $personaje['num_episodio'] ?>)</p>
                        <p><strong>Lugar:</strong> <?= $personaje['lugar'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No se encontraron personajes que coincidan con los criterios de búsqueda.</p>
        <?php endif; ?>
    </main>
</body>
</html>
