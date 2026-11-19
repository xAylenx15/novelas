<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/MVC/controllers/NovelasController.php";
require_once __DIR__ . "/MVC/controllers/CapitulosController.php";

$novelasController = new NovelasController();
$capitulosController = new CapitulosController();

$action = $_GET['action'] ?? 'index';

switch ($action) {

    // ==========================================
    // --- RUTAS DE NOVELAS ---
    // ==========================================
    
    case 'index':
        $novelasController->index();
        break;

    case 'showNovela':
        $novelasController->show();
        break;

    case 'create':
        $novelasController->create();
        break;

    case 'store':
        $novelasController->store();
        break;

    case 'edit':
        $novelasController->edit();
        break;

    case 'update':
        $novelasController->update();
        break;

    case 'delete':
        $novelasController->delete();
        break;


    // ==========================================
    // --- RUTAS DE CAPÍTULOS ---
    // ==========================================

    case 'showCapitulo':
        $capitulosController->show();
        break;

    case 'crearCapitulo':
        $capitulosController->create();
        break;

    case 'storeCapitulo':
        $capitulosController->store();
        break;

    case 'editarCapitulo':
        $capitulosController->editar();
        break;

    case 'updateCapitulo':
        $capitulosController->update();
        break;

    case 'deleteCapitulo':
        $capitulosController->delete();
        break;


    // ==========================================
    // --- RUTA POR DEFECTO ---
    // ==========================================
    
    default:
        $novelasController->index();
        break;
}
?>
