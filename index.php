<!DOCTYPE html>
<html>

<head>
    <title> Mis Novelas - Inicio </title>
    <!-- Traemos Bootstrap para que me ayude en css  -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style2.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <!-- Cabecera de la página (Puro HTML visual) -->
        <div class="jumbotron" style="background: #000 ; border: 1px solid #000;">
            <h1> Mi biblioteca de traducciones</h1>
            <p> Listado de novelas traducidas</p>
            <!-- Rutas actualizadas hacia la carpeta view -->
            <a href="MVC/view/subir.php" class="btn btn-outline-light">+ Subir Nuevo Capítulo</a>
            <a href="MVC/view/subir_novela.php" class="btn btn-outline-light">+ Subir Nueva Novela</a>
        </div>
    </div>
    <div class="row">
        <?php foreach ($novel as $row): ?>
            <div class="col-md-4 mb-4">
                <div class="card bg-dark text-white p-3 border-secondary h-100 d-flex flex-column">
                    <div class="text-center">
                        <img src="<?php echo $row['portada']; ?>" class="img-fluid rounded mb-3"
                            style="height: 250px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['titulo']; ?></h5>
                        <p class="card-text"><?php echo substr($row['sinopsis'], 0, 120) . "..."; ?></p>
                        <div class="mt-auto">
                            <!-- Ruta actualizada hacia la carpeta view -->
                            <a href="/index.php?action=showNovela&id=<?php echo $row['id_novela']; ?>"
                                class="btn btn-primary">Ver novela</a>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>