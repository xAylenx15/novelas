<?php
require_once __DIR__ . "/../configuration/conexion.php";
require_once __DIR__ . "/../models/CapitulosModels.php";
// ¡Borramos el require de la vista que estaba acá arriba!

class CapitulosController
{
    private $capitulosModel; 

    public function __construct()
    { 
        global $conn;
        $this->capitulosModel = new Capitulo($conn);
    }
    
    // Muestra el formulario visual
    public function create()
    { 
        require __DIR__ . "/../view/subir.php";
    }

    // Atrapa los datos del formulario y los guarda
    public function store()
    { 
        $novela_id = (int) ($_POST["novela_id"] ?? 0);
        $titulo_cap = $_POST["titulo_cap"] ?? "";
        $numero = (int) ($_POST["numero"] ?? 0);
        $contenido = $_POST["contenido"] ?? "";

        // Validamos que hayan llenado todo
        if ($novela_id <= 0 || $titulo_cap === "" || $contenido === "") {
            die("Error: Faltan datos para subir el capítulo.");
        }

        // Lo mandamos a guardar al modelo
        $this->capitulosModel->create($novela_id, $titulo_cap, $numero, $contenido);
        
        // Lo mandamos al inicio (o podrías mandarlo de vuelta a ver_novela.php)
        header("Location: /index.php"); 
        exit;
    }
    // 3. Elimina el capítulo
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['capitulo_id'];
            
            // Primero buscamos el capítulo para saber a qué novela pertenece (así sabemos a dónde volver)
            $capitulo = $this->capitulosModel->getCapituloById($id);
            $id_novela = $capitulo['id_novela'];
            
            // Llamamos a la función de tu modelo que ejecuta fn_bajaCapitulo
            $this->capitulosModel->deleteCapitulo($id);
            
            // Redirigimos a la pantalla de la novela (la lista de capítulos)
            header("Location: index.php?action=showNovela&id=" . $id_novela);
            exit();
        }
    }
    // Muestra el capítulo para leer
   public function show()
    {
        $id = (int) ($_GET["id"] ?? 0);
        if ($id <= 0) {
            die("ID de capítulo inválido");
        }
        
        // CORRECCIÓN: Tu modelo usa getById()
        $capitulo = $this->capitulosModel->getCapituloById($id);
        if (!$capitulo) {
            die("Capítulo no encontrado");
        }
        
        // Buscamos los capítulos anterior y siguiente
        $prev = $this->capitulosModel->getPrevChapter($capitulo['id_novela'], $capitulo['numero']);
        $next = $this->capitulosModel->getNextChapter($capitulo['id_novela'], $capitulo['numero']);
        
        // Extraemos solo los IDs numéricos (si no hay, queda en null)
        $prev_id = $prev ? $prev['capitulo_id'] : null;
        $next_id = $next ? $next['capitulo_id'] : null;
        
        require __DIR__ . "/../view/leer_capitulo.php";
    }

    // 1. Muestra la pantalla de edición
  // 1. Muestra la pantalla de edición (Versión a prueba de fallos)
    public function editar() {
        // Obligamos a que el ID sea un número válido
        $id = (int) ($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            die("ERROR: No llegó el ID en la URL. Volvé a la lista de capítulos y hacé clic en 'Editar' de nuevo.");
        }
        
        // Buscamos el capítulo
        $capitulo = $this->capitulosModel->getCapituloById($id); 
        
        if (!$capitulo) {
            die("ERROR: Se buscó el capítulo con ID $id, pero la base de datos devolvió vacío. ¿Existe ese ID?");
        }
        
        // Si pasa las dos validaciones, cargamos la vista
        require_once __DIR__ . '/../view/editarCapitulo.php';
    }

    // 2. Guarda los cambios en la base de datos
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Atrapamos los datos del formulario
            $id = $_POST['capitulo_id'];
            $nombre = $_POST['nombre'];
            $numero = $_POST['numero'];
            $texto = $_POST['texto'];
            
            // Llamamos al modelo para que haga el UPDATE
            $this->capitulosModel->updateCapitulo($id, $nombre, $numero, $texto);
            
            // Redirigimos al usuario a leer el capítulo ya actualizado
            header("Location: index.php?action=showCapitulo&id=" . $id);
            exit();
        }
    }
}
?>