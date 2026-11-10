<?php
require __DIR__ . "/../configuration/conexion.php";
class Novela
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM novelas ORDER BY id_novela DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // devuelve un array, no un statement
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM novelas WHERE id_novela = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($titulo, $sinopsis, $portada, $estado, $genero,$autor)
    {
        // Como es una función que devuelve algo, usamos SELECT en vez de CALL
        $sql = "SELECT fn_altaDeNovelas(:titulo, :sinopsis, :portada , :estado , :genero, :autor) AS nuevo_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':titulo' => $titulo,
            ':sinopsis' => $sinopsis,
            ':portada' => $portada,
            ':estado' => $estado,
            ':genero' => $genero,
            ':autor' => $autor
        ]);

        // Atrapamos el resultado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $idGenerado = $resultado['nuevo_id'];

        return $idGenerado; // Se lo mandás al Controlador para que haga la redirección
    }

   public function update($id, $titulo, $sinopsis, $portada)
    {
        $datos = [
            'titulo' => $titulo,
            'sinopsis' => $sinopsis,
            'portada' => $portada
        ];

        $json_datos = json_encode($datos);

        // ACÁ ESTÁ EL CAMBIO: Usamos CAST() en vez de ::jsonb
        $sql = "SELECT * FROM fn_modificar_novela(:id, CAST(:datos_json AS jsonb))";
        
        $stmt = $this->conn->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':datos_json' => $json_datos
        ]);
    }

   public function delete($id) {
    // Usamos SELECT porque la función devuelve un texto
    $sql = "SELECT fn_baja_novela(:id) AS mensaje_resultado";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    // Atrapamos la frase que mandó PostgreSQL
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Devuelve 'Novela eliminada correctamente' o 'La novela no existe'
    return $resultado['mensaje_resultado']; 
}
}