<?php
$host = 'localhost';
$port = '5432'; // Puerto clásico de PostgreSQL
$dbname = 'Biblioteca'; // Reemplazá por el nombre exacto de tu BD
$user = 'postgres'; // Usuario por defecto
$password = '12345678Aylen.'; // La clave que usás para entrar a pgAdmin o DBeaver

try {
    // La magia ocurre acá: pasamos de 'mysql:' a 'pgsql:'
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $user, $password);
    
    // Le decimos a PDO que lance excepciones si algo falla para poder atraparlas
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Error de conexión a PostgreSQL: " . $e->getMessage());
}
?>