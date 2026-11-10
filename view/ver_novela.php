<!DOCTYPE html>
<html>

<head>
    <title> <?php echo $novela['titulo']; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #000 ; color: #fff; padding: 20px;">
    <div class="container mt-5">
        <!-- RUTA CORREGIDA ACÁ -->
        <a class="btn btn-outline-light mb-4" href="../../index.php">← Volver al catálogo</a>

        <div class="row justify-content-center">
            <div class="col-md-8 bg-dark p-4 rounded border border-secondary text-center">

                <h1 class="text-white mb-4 fw-bold"><?php echo $novela['titulo']; ?></h1>

                <img src="<?php echo $novela['portada']; ?>" class="img-fluid rounded mb-4 shadow-lg"
                    style="max-height: 500px; object-fit: cover;" alt="Portada de <?php echo $novela['titulo']; ?>">

                <!-- 1. Estado y Género (Arriba de la sinopsis, chiquitos y con otra letra) -->
                <div class="mb-4 d-flex justify-content-center gap-4"
                    style="font-family: 'Courier New', Courier, monospace; font-size: 1.15rem; letter-spacing: 0.5px;">

                    <!-- Pastilla Blanca para el Estado -->
                    <span class="badge rounded-pill px-4 py-2 text-dark text-uppercase shadow-sm"
                        style="background-color: white;">
                        <?php echo $novela['estado']; ?>
                    </span>

                    <!-- Pastilla Negra para el Género (con borde blanco para que no se pierda en el fondo) -->
                    <span class="badge rounded-pill px-4 py-2 text-white text-uppercase shadow-sm"
                        style="background-color: black; border: 1px solid white;">
                        <?php echo $novela['genero']; ?>
                    </span>

                </div>
                <h4 class="text-secondary mb-3 text-start border-bottom border-secondary pb-2">Sinopsis</h4>
                <p class="text-light text-start fs-5" style="line-height: 1.7;">
                    <?php echo ($novela['sinopsis']); ?>
                </p>
                <div class="mt-3 text-end text-white-50 fs-5">
                    <strong>Escrito por:</strong> <?php echo $novela['autor']; ?>
                </div>
                <a href="/index.php?action=edit&id=<?php echo $novela['id_novela']; ?>"
                    class="btn btn-outline-light">Editar Novela</a>
                <hr class="border-secondary my-5">

                <h3 class="text-white mb-4 text-start">Capítulos</h3>


                <div class="list-group text-start shadow-sm mb-4">
                    <?php if (count($capitulos) > 0): ?>
                        <?php foreach ($capitulos as $cap): ?>

                            <!-- Transformamos el <a> en un <div> y le agregamos d-flex justify-content-between para separar los elementos -->
                            <div
                                class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">

                                <!-- 1. Enlace principal para LEER el capítulo -->
                                <a href="/index.php?action=showCapitulo&id=<?php echo $cap['capitulo_id']; ?>"
                                    class="text-light text-decoration-none">
                                    Capitulo
                                    <?php echo $cap['numero']; ?>
                                </a>

                                <!-- 2. Botón para EDITAR el capítulo -->
                                <a href="/index.php?action=editarCapitulo&id=<?php echo $cap['capitulo_id']; ?>"
                                    class="btn btn-sm btn-outline-light px-3">
                                    Editar
                                </a>

                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-secondary text-center" role="alert">
                            <em>Aún no hay capítulos subidos para esta novela.</em>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>