<?php
require_once __DIR__ . "/../configuration/conexion.php";
require_once __DIR__ . "/../models/NovelasModels.php";
class NovelasController
{
    private $novelModel; //atributo que se usa para moverse

    public function __construct()
    { // un constructor para que vaya preparando el terreno
        global $conn;
        $this->novelModel = new Novela($conn);
    }
    public function index() {
        $novel = $this->novelModel->getAll();
        require __DIR__ . "/../view/index.php"; // ESTA ES LA MAGIA
    }
    public function show()
    {
        // 1. Atrapamos el ID
        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            header("Location: /index.php");
            exit();
        }

        // 2. Buscamos la novela
        $novela = $this->novelModel->getById($id);
        if (!$novela) {
            die("<h2 style='color:red; text-align:center;'>Error: La novela no existe.</h2>");
        }

        // 3. Buscamos los capítulos de esta novela instanciando el modelo de capítulos
        require_once __DIR__ . "/../models/CapitulosModels.php";
        global $conn;
        $capituloModel = new Capitulo($conn);
        
        // Usamos la función getByNovelaId que habíamos creado antes
        $capitulos = $capituloModel->getByNovelaId($id);

        // 4. Mandamos todo a la vista limpia
        require __DIR__ . "/../view/ver_novela.php";
    }
    public function create()
    { //sirve para agregar el archivo donde cargado el formulario
        require 'subir_novela.php';
    }

   public function store() {
    
    $titulo = trim($_POST['titulo'] ?? '');
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $portada = trim($_POST['portada'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $autor = trim($_POST['autor'] ?? '');

    if (empty($titulo) || empty($portada) || empty($genero)) {
        header("Location: /index.php?action=create&error=vacios");
        exit();
    }

    // 3. Recién acá, sabiendo que los datos están perfectos, llamamos al Modelo
    // (Aca llamarías a la función o procedimiento de tu base de datos)
    $nuevo_id = $this->novelModel->create($titulo, $sinopsis, $portada, $estado,$genero,$autor);

    header("Location: /index.php?action=showNovela&id=" . $nuevo_id);
    exit();
}
    public function edit()
    { //se encarga de buscar el id y saber si esta dicho id
        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            die("ID invalido");
        }
        $novel = $this->novelModel->getById($id);
        if (!$novel) {
            die("Novela no encontrada");
        }
        require __DIR__ . "/../view/edit.php";
    }

    public function update()
    {  //se encarga de atrapar los datos del update y mandarlo a update en model, pa que actualice
        $id = (int) ($_POST["novela_id"] ?? 0);
        $titulo = $_POST["titulo"];
        $sinopsis = $_POST["sinopsis"];
        $portada = $_POST["portada"];
        if ($id <= 0 || $titulo === "" || $portada === "") {
            die("Datos inválidos.");
        }
        $this->novelModel->update($id, $titulo, $sinopsis, $portada);
        header("Location: index.php?action=index");
        exit();
    }

    public function delete()
    {
        $id = (int) ($_POST["novela_id"] ?? 0);
        if ($id > 0) {
            $this->novelModel->delete($id);
            header("Location: index.php?action=index");
            exit;
        }
    }

}
?>