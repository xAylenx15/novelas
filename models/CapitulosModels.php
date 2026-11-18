<?php 
require_once __DIR__ ."/../configuration/conexion.php"; 

class Capitulo {
    private $conn;
    
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getByNovelaId($novela_id){
        // Leemos de la vista en lugar de la tabla cruda
        $stmt = $this->conn->prepare("SELECT * FROM vw_capitulos_completos WHERE id_novela = :id_novela ORDER BY numero ASC");
        $stmt->execute([':id_novela' => $novela_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCapituloById($id){
        // Leemos de la vista
        $stmt = $this->conn->prepare("SELECT * FROM fn_get_capitulo(:id)");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($novela_id, $nombre, $numero, $texto){
        // Atrapamos el ID generado
        $sql = "SELECT fn_altaCapitulo(:id_novela, :nombre, :numero, :texto) AS nuevo_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id_novela' => $novela_id,
            ':nombre' => $nombre,
            ':numero' => $numero,
            ':texto' => $texto
        ]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['nuevo_id'];
    }

    public function deleteCapitulo($id){
        // Atrapamos el mensaje de éxito de la base de datos
        $sql = "SELECT fn_bajaCapitulo(:id) AS mensaje";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['mensaje'];
    }
    
    public function getPrevChapter($novela_id, $numero_actual){
        // Leemos de la vista
        $stmt = $this->conn->prepare("SELECT capitulo_id FROM vw_capitulos_completos WHERE id_novela = :id_novela AND numero < :numero ORDER BY numero DESC LIMIT 1");
        $stmt->execute([':id_novela' => $novela_id, ':numero' => $numero_actual]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getNextChapter($novela_id, $numero_actual){
        // Leemos de la vista
        $stmt = $this->conn->prepare("SELECT capitulo_id FROM vw_capitulos_completos WHERE id_novela = :id_novela AND numero > :numero ORDER BY numero ASC LIMIT 1");
        $stmt->execute([':id_novela' => $novela_id, ':numero' => $numero_actual]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCapitulo($id, $nombre, $numero, $texto) {
        // La actualización siempre usa la función fuerte, esto no cambia
        $sql = "SELECT fn_modificarCapitulo(:id, :nombre, :numero, :texto)";
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            ':id'     => $id,
            ':nombre' => $nombre,
            ':numero' => $numero,
            ':texto'  => $texto
        ]);
    }
} 
?>
