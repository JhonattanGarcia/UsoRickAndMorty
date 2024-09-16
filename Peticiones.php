<?php
require_once 'Conexion.php'; // Asegúrate de que la clase Conexion esté disponible

class Peticiones {
    private $conexion;

    // Constructor para inicializar la conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para obtener todos los personajes (puedes agregar paginación si es necesario)
    public function obtenerPersonajes($page = 1) {
        $query = '
        {
            characters(page: ' . $page . ') {
                info {
                    count
                    pages
                    next
                    prev
                }
                results {
                    id
                    name
                    status
                    species
                    gender
                    origin {
                        name
                    }
                    type
                    image
                }
            }
        }';
        $response = $this->conexion->ejecutarQuery($query);
        // Comprobamos si la respuesta es válida y devolvemos los resultados
        return isset($response['data']['characters']['results']) ? $response['data']['characters']['results'] : [];
    }

    // Método para obtener detalles de un personaje específico por ID
    public function obtenerPersonajePorId($id) {
        $query = '
        {
            character(id: ' . $id . ') {
                id
                name
                status
                species
                type
                gender
                origin {
                    name
                }
                image
                episode {
                    name
                    episode
                }
            }
        }';
        $response = $this->conexion->ejecutarQuery($query);
        // Comprobamos si la respuesta es válida y devolvemos los detalles del personaje
        return isset($response['data']['character']) ? $response['data']['character'] : null;
    }

    // Método para buscar personajes por nombre
    public function buscarPersonajesPorNombre($nombre) {
        $query = '
        {
            characters(filter: { name: "' . $nombre . '" }) {
                results {
                    id
                    name
                    species
                    status
                    gender
                    origin {
                        name
                    }
                    image
                }
            }
        }';
        $response = $this->conexion->ejecutarQuery($query);
        // Comprobamos si la respuesta es válida y devolvemos los resultados
        return isset($response['data']['characters']['results']) ? $response['data']['characters']['results'] : [];
    }
}
?>
