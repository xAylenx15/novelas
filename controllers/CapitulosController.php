<?php
require_once __DIR__ . "/../configuration/conexion.php";
require_once __DIR__ . "/../models/CapitulosModels.php";
require_once __DIR__ . "/../configuration/errores.php";

class CapitulosController
{
    private $capitulosModel;

    public function __construct()
    {
        global $conn;
        $this->capitulosModel = new Capitulo($conn);
    }

    // 1. Muestra el formulario de Alta
    public function create()
    {
        $id_novela = $_GET['id_novela'] ?? 0;
        require __DIR__ . "/../view/subir.php";
    }

    // 2. Atrapa los datos del Alta
    public function store()
    {
        $novela_id = (int) ($_POST["novela_id"] ?? 0);
        $titulo_cap = $_POST["titulo_cap"] ?? null;
        $numero = (($_POST["numero"] ?? '') === '') ? null : (int) $_POST["numero"];
        $contenido = $_POST["contenido"] ?? null;

        try {
            // Si hay un error de negocio, Postgre dispara el catch
            $this->capitulosModel->create($novela_id, $titulo_cap, $numero, $contenido);
            header("Location: index.php?action=showNovela&id=" . $novela_id);
            exit;
        } catch (PDOException $e) {
            $error = mensajeBD($e); // Atrapamos el RAISE EXCEPTION
            $id_novela = $novela_id;
            require __DIR__ . "/../view/subir.php";
        }
    }

    // 3. Elimina el capítulo
    public function delete()
    {
        $id = $_POST['capitulo_id'] ?? null;

        try {
            // Buscamos para saber a dónde redirigir después
            $capitulo = $this->capitulosModel->getCapituloById($id);
            $id_novela = $capitulo['id_novela'];

            $this->capitulosModel->deleteCapitulo($id);

            header("Location: index.php?action=showNovela&id=" . $id_novela);
            exit();
        } catch (PDOException $e) {
            die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e)) . "</h2>");
        }
    }

    // 4. Muestra el capítulo para leer
    public function show()
    {
        $id = (int) ($_GET["id"] ?? 0);

        try {
            $capitulo = $this->capitulosModel->getCapituloById($id);
        } catch (PDOException $e) {
            die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e)) . "</h2>");
        }

        // Buscamos los capítulos anterior y siguiente
        $prev = $this->capitulosModel->getPrevChapter($capitulo['id_novela'], $capitulo['numero']);
        $next = $this->capitulosModel->getNextChapter($capitulo['id_novela'], $capitulo['numero']);

        $prev_id = $prev ? $prev['capitulo_id'] : null;
        $next_id = $next ? $next['capitulo_id'] : null;

        require __DIR__ . "/../view/leer_capitulo.php";
    }

    // 5. Muestra la pantalla de edición
    public function editar()
    {
        $id = (int) ($_GET['id'] ?? 0);

        try {
            $capitulo = $this->capitulosModel->getCapituloById($id);
        } catch (PDOException $e) {
            die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e)) . "</h2>");
        }


        require_once __DIR__ . '/../view/editarCapitulo.php';
    }

    // 6. Guarda los cambios en la base de datos
    public function update()
    {
        $id = $_POST['capitulo_id'] ?? null;
        $id_novela = $_POST['id_novela'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $numero = (($_POST["numero"] ?? '') === '') ? null : (int) $_POST["numero"];
        $texto = $_POST['texto'] ?? null;

        try {
            $this->capitulosModel->updateCapitulo($id, $id_novela, $nombre, $numero, $texto);

            header("Location: index.php?action=showCapitulo&id=" . $id);
            exit();
        } catch (PDOException $e) {

            $error = mensajeBD($e);

            try {
                $capitulo = $this->capitulosModel->getCapituloById($id);
            } catch (PDOException $e2) {
                die("<h2 style='color:red; text-align:center;'>" . htmlspecialchars(mensajeBD($e2)) . "</h2>");
            }

            $capitulo['nombre'] = $nombre;
            $capitulo['numero'] = $numero;
            $capitulo['texto'] = $texto;

            require_once __DIR__ . '/../view/editarCapitulo.php';
        }
    }
}
?>
