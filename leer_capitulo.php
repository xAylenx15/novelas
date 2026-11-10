<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title Capitulo <?php echo $capitulo['numero']; ?>> - <?php echo $capitulo['nombre']; ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #121212; color: #fff; ">
    <!-- Cambiamos container por container-fluid -->
    <div class="container-fluid p-4 p-md-5 bg-dark min-vh-100">

        <!-- Cambiamos showCapitulo por showNovela y capitulo_id por id_novela -->
        <a class="btn btn-outline-light mb-3"
            href="index.php?action=showNovela&id=<?php echo $capitulo['id_novela']; ?>" role="button">← Volver</a>
        </a>


        <div class="w-100">

            <div class="text-center mb-5 border-bottom border-secondary pb-3">
                <h5 class="text-secondary">Capítulo <?php echo $capitulo['numero']; ?></h5>
                <h1 class="text-white fw-bold"><?php echo $capitulo['nombre']; ?></h1>
            </div>

            <!-- Le puse px-3 para celulares y px-lg-5 para compus, así el texto respira un poco de los bordes -->
            <div class="text-light fs-5 px-3 px-lg-5" style="line-height: 1.8; text-align: justify;">
                <?php echo nl2br($capitulo['texto']); ?>
            </div>

            <div
                class="d-flex justify-content-between align-items-center my-4 p-3 bg-dark rounded border border-secondary">

                <!-- Botón Anterior: Solo aparece si $prev_id tiene algo -->
                <div class="w-25 text-start">
                    <?php if (!empty($prev_id)): ?>
                        <a href="/index.php?action=showCapitulo&id=<?php echo $prev_id; ?>"
                            class="btn btn-outline-light w-100">← Anterior</a>
                    <?php endif; ?>
                </div>

                <!-- Botón Central (Ir a la Novela) -->
                <div class="w-25 text-center">
                    <!-- Apunta directamente a tu archivo visual de la novela -->
                    <a href="/index.php?action=showNovela&id=<?php echo $capitulo['id_novela']; ?>"
                        class="btn btn-primary w-100">Ir a la Novela</a>
                </div>

                <!-- Botón Siguiente: Solo aparece si $next_id tiene algo -->
                <div class="w-25 text-end">
                    <?php if (!empty($next_id)): ?>
                        <a href="/index.php?action=showCapitulo&id=<?php echo $next_id; ?>"
                            class="btn btn-outline-light w-100">Siguiente →</a>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

</body>

</html>