<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <!-- CORRECCIÓN 1: La etiqueta title ahora está bien formada y segura -->
    <title>Capítulo <?php echo htmlspecialchars($capitulo['numero']); ?> - <?php echo htmlspecialchars($capitulo['nombre']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #121212; color: #fff; ">
    <div class="container-fluid p-4 p-md-5 bg-dark min-vh-100">

        <!-- CORRECCIÓN 2: Se borró el </a> que sobraba -->
        <a class="btn btn-outline-light mb-3"
            href="index.php?action=showNovela&id=<?php echo htmlspecialchars($capitulo['id_novela']); ?>" role="button">← Volver</a>

        <div class="w-100">
            <div class="text-center mb-5 border-bottom border-secondary pb-3">
                <h5 class="text-secondary">Capítulo <?php echo htmlspecialchars($capitulo['numero']); ?></h5>
                <h1 class="text-white fw-bold"><?php echo htmlspecialchars($capitulo['nombre']); ?></h1>
            </div>

            <!-- CORRECCIÓN 3: Se envolvió el texto en htmlspecialchars antes de hacerle el nl2br -->
            <div class="text-light fs-5 px-3 px-lg-5" style="line-height: 1.8; text-align: justify;">
                <?php echo nl2br(htmlspecialchars($capitulo['texto'])); ?>
            </div>

            <div class="d-flex justify-content-between align-items-center my-4 p-3 bg-dark rounded border border-secondary">

                <div class="w-25 text-start">
                    <?php if (!empty($prev_id)): ?>
                        <a href="/index.php?action=showCapitulo&id=<?php echo htmlspecialchars($prev_id); ?>"
                            class="btn btn-outline-light w-100">← Anterior</a>
                    <?php endif; ?>
                </div>

                <div class="w-25 text-center">
                    <a href="/index.php?action=showNovela&id=<?php echo htmlspecialchars($capitulo['id_novela']); ?>"
                        class="btn btn-primary w-100">Ir a la Novela</a>
                </div>

                <div class="w-25 text-end">
                    <?php if (!empty($next_id)): ?>
                        <a href="/index.php?action=showCapitulo&id=<?php echo htmlspecialchars($next_id); ?>"
                            class="btn btn-outline-light w-100">Siguiente →</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
