<?php 
require_once __DIR__ ."/../configuration/conexion.php"; 

class Capitulo {
    private $conn;
    
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getByNovelaId($novela_id){
        $stmt = $this->conn->prepare("SELECT * FROM capitulos WHERE id_novela = :id_novela ORDER BY numero ASC");
        $stmt->execute([':id_novela' => $novela_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCapituloById($id){
        $stmt = $this->conn->prepare("SELECT * FROM capitulos WHERE capitulo_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($novela_id, $nombre, $numero, $texto){
        // Llamamos a la función de Postgre con los parámetros correctos
        $sql = "SELECT fn_altaCapitulo(:id_novela, :nombre, :numero, :texto)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id_novela' => $novela_id,
            ':nombre' => $nombre,
            ':numero' => $numero,
            ':texto' => $texto
        ]);
    }

    public function deleteCapitulo($id){
        $sql = "SELECT fn_bajaCapitulo(:id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getPrevChapter($novela_id, $numero_actual){
        $stmt = $this->conn->prepare("SELECT capitulo_id FROM capitulos WHERE id_novela = :id_novela AND numero < :numero ORDER BY numero DESC LIMIT 1");
        $stmt->execute([':id_novela' => $novela_id, ':numero' => $numero_actual]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getNextChapter($novela_id, $numero_actual){
        $stmt = $this->conn->prepare("SELECT capitulo_id FROM capitulos WHERE id_novela = :id_novela AND numero > :numero ORDER BY numero ASC LIMIT 1");
        $stmt->execute([':id_novela' => $novela_id, ':numero' => $numero_actual]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateCapitulo($id, $nombre, $numero, $texto) {
        // Armamos la consulta SQL clásica para actualizar
        $sql = "UPDATE capitulos 
                SET nombre = :nombre, 
                    numero = :numero, 
                    texto = :texto 
                WHERE capitulo_id = :id";
                
        $stmt = $this->conn->prepare($sql);
        
        // Ejecutamos pasando los parámetros para evitar inyecciones SQL
        return $stmt->execute([
            ':nombre' => $nombre,
            ':numero' => $numero,
            ':texto'  => $texto,
            ':id'     => $id
        ]);
    }
} 
?>