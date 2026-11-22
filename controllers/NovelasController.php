<?php
require_once __DIR__ . "/../configuration/conexion.php";
require_once __DIR__ . "/../models/NovelasModels.php";
require_once __DIR__ . "/../configuration/errores.php";

class NovelasController
{
    private $novelModel;

    public function __construct()
    {
        global $conn;
        $this->novelModel = new Novela($conn);
    }

    public function index()
    {
        $novel = $this->novelModel->getAll();
        require __DIR__ . "/../view/index.php";
    }

    public function show()
    {
        $id = $_GET["id"] ?? null;
        try {
            $novela = $this->novelModel->getById($id);
        } catch (PDOException $e) {
            die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e)) . "</h2>");
        }

        require_once __DIR__ . "/../models/CapitulosModels.php";
        global $conn;
        $capituloModel = new Capitulo($conn);
        $capitulos = $capituloModel->getByNovelaId($id);

        require __DIR__ . "/../view/ver_novela.php";
    }

    public function create()
    {
        // Pasamos las listas para los <select> también al formulario de Alta
        $estados = $this->novelModel->getAllEstados();
        $generos = $this->novelModel->getAllGeneros();
        require __DIR__ . "/../view/subir_novela.php";
    }

    public function store()
    {
        // Atrapamos crudo, sin validaciones
        $titulo = $_POST['titulo'] ?? null;
        $sinopsis = $_POST['sinopsis'] ?? null;
        $portada = $_POST['portada'] ?? null;
        $estado = $_POST['estado'] ?? null;
        $genero = $_POST['genero'] ?? null;
        $autor = $_POST['autor'] ?? null;

        try {
            // La base de datos decide si esto sirve o lanza excepción
            $nuevo_id = $this->novelModel->create($titulo, $sinopsis, $portada, $estado, $genero, $autor);
            header("Location: /index.php?action=showNovela&id=" . $nuevo_id);
            exit();
        } catch (PDOException $e) {
            $error = mensajeBD($e);
            $estados = $this->novelModel->getAllEstados();
            $generos = $this->novelModel->getAllGeneros();
            require __DIR__ . "/../view/subir_novela.php";
        }
    }
    public function consulta()
{
    $titulo    = $_GET['titulo'] ?? '';
    $autor     = $_GET['autor'] ?? '';
    $id_genero = $_GET['genero'] ?? '';
    $id_estado = $_GET['estado'] ?? '';

    $generos = $this->novelModel->getAllGeneros();
    $estados = $this->novelModel->getAllEstados();
    $novelas = $this->novelModel->buscarNovelas($titulo, $autor, $id_genero, $id_estado);

    require_once __DIR__ . '/../view/consulta.php';
}

    public function edit()
    {
        $id = $_GET["id"] ?? null;

        try {
            $novel = $this->novelModel->getById($id);
        } catch (PDOException $e) {
            die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e)) . "</h2>");
        }

        $estados = $this->novelModel->getAllEstados();
        $generos = $this->novelModel->getAllGeneros();

        require __DIR__ . "/../view/edit.php";
    }

    public function update()
    {
        // Atrapamos crudo
        $id = $_POST["novela_id"] ?? null;
        $titulo = $_POST["titulo"] ?? null;
        $sinopsis = $_POST["sinopsis"] ?? null;
        $portada = $_POST["portada"] ?? null;
        $estado = $_POST["estado"] ?? null;
        $genero = $_POST["genero"] ?? null;
        $autor = $_POST["autor"] ?? null;

        try {
            $this->novelModel->update($id, $titulo, $sinopsis, $portada, $estado, $genero, $autor);
            header("Location: index.php?action=showNovela&id=" . $id);
            exit();
        } catch (PDOException $e) {
            $error = mensajeBD($e);
            try {
                $novel = $this->novelModel->getById($id);
            } catch (PDOException $e2) {
                die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e2)) . "</h2>");
            }

            $novel['titulo']    = $titulo;
            $novel['sinopsis']  = $sinopsis;
            $novel['portada']   = $portada;
            $novel['autor']     = $autor;
            $novel['id_estado'] = $estado;
            $novel['id_genero'] = $genero;

            $estados = $this->novelModel->getAllEstados();
            $generos = $this->novelModel->getAllGeneros();
            require __DIR__ . "/../view/edit.php";
        }

    }

    public function delete()
    {
        $id = $_POST["novela_id"] ?? null;
        try {
            $this->novelModel->delete($id);
            header("Location: index.php?action=index");
            exit;
        } catch (PDOException $e) {
            $error = mensajeBD($e);
            die("<h2 style='color:red;'>Error de Base de Datos al eliminar: " . htmlspecialchars($error) . "</h2>");
        }
    }
}
?>
