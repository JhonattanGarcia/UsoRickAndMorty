<?php
class Conexion {
    private $apiUrl = "https://rickandmortyapi.com/graphql"; // URL base de la API
    private $headers = [
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    // Método para inicializar la conexión
    private function initConexion($query) {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['query' => $query]));

        return $ch;
    }

    // Método para ejecutar la petición
    public function ejecutarQuery($query) {
        $ch = $this->initConexion($query);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error en la conexión: ' . curl_error($ch);
            return null;
        }

        curl_close($ch);
        return json_decode($response, true);
    }
}
?>
