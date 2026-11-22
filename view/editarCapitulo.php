<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar: <?php echo htmlspecialchars($capitulo['nombre'] ?? ''); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #121212; color: #fff; padding: 20px;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-white p-4 border-secondary">

                    <a class="btn btn-outline-light mb-3"
                        href="index.php?action=showNovela&id=<?php echo $capitulo['id_novela']; ?>" role="button">←
                        Volver</a>

                    <h3 class="mb-4 text-center">Gestión de Capítulo</h3>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Atención:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form id="formCapitulo" action="index.php?action=updateCapitulo" method="POST">

                        <input type="hidden" name="capitulo_id" value="<?php echo $capitulo['capitulo_id']; ?>">
                        <input type="hidden" name="id_novela" value="<?= htmlspecialchars($capitulo['id_novela']) ?>">

                        <div class="mb-3">
                            <label class="form-label text-white">Título del Capitulo</label>
                            <input type="text" name="nombre" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($capitulo['nombre'] ?? ''); ?>">

                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Número</label>
                            <input type="number" id="num" name="numero"
                                class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($capitulo['numero'] ?? ''); ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white">Contenido</label>
                            <textarea name="texto" class="form-control bg-secondary text-white border-0"
                                rows="10"><?php echo htmlspecialchars($capitulo['texto'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" name="accion" value="editar" class="btn btn-primary px-4">
                                Guardar Cambios
                            </button>
                    </form>

                    <form action="index.php?action=deleteCapitulo" method="POST" class="m-0">
                        <input type="hidden" name="capitulo_id" value="<?php echo $capitulo['capitulo_id']; ?>">
                        <button type="submit" class="btn btn-danger px-4"
                            onclick="return confirm('¿Estás seguro de que querés borrar este capitulo? Esto no se puede deshacer.');">
                            Eliminar Capitulo
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>
