<?php
require_once __DIR__ . "/../configuration/conexion.php";

class Novela
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $stmt = $this->conn->query("SELECT * FROM vw_novelas_completas ORDER BY id_novela DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM fn_get_novela(:id)");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllEstados()
    {
        $stmt = $this->conn->query("SELECT * FROM estados ORDER BY id_estado");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllGeneros()
    {
        $stmt = $this->conn->query("SELECT * FROM generos ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($titulo, $sinopsis, $portada, $id_estado, $id_genero, $autor)
    {
        $sql = "SELECT fn_altaDeNovelas(:titulo, :sinopsis, :portada, :estado, :genero, :autor) AS nuevo_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':titulo' => $titulo,
            ':sinopsis' => $sinopsis,
            ':portada' => $portada,
            ':estado' => ($id_estado === '' ? null : $id_estado),
            ':genero' => ($id_genero === '' ? null : $id_genero),
            ':autor' => $autor
        ]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['nuevo_id'];

    }

    public function update($id, $titulo, $sinopsis, $portada, $id_estado, $id_genero, $autor)
    {
        // Le agregamos (int) solo para que json_encode los trate como números
        $datos = [
            'titulo' => $titulo,
            'sinopsis' => $sinopsis,
            'portada' => $portada,
            'id_estado' => (int) $id_estado,
            'id_genero' => (int) $id_genero,
            'autor' => $autor
        ];

        $json_datos = json_encode($datos);

        $sql = "SELECT * FROM fn_modificar_novela(:id, CAST(:datos_json AS jsonb))";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':datos_json' => $json_datos
        ]);
    }

    public function delete($id)
    {
        $sql = "SELECT fn_baja_novela(:id) AS mensaje_resultado";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['mensaje_resultado'];
    }
}
?>
