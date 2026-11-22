<!DOCTYPE html>
<html>

<head>
    <title>Subir Capítulo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ruta corregida al CSS -->
    <link href="../../style2.css" rel="stylesheet">
</head>

<body style="background: #000; padding: 20px;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-white p-4 border-secondary">

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Atención:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- La ruta va directo a index.php -->
                    <form action="index.php?action=storeCapitulo" method="POST">

                        <!-- El botón volver también usa index.php y le pasamos el ID para que vuelva a la novela -->
                        <a class="btn btn-outline-light mb-3"
                            href="index.php?action=showNovela&id=<?php echo htmlspecialchars($id_novela); ?>"
                            role="button"> ← Volver</a>

                        <div class="mb-3">
                            <!-- Este input oculto ahora sí va a recibir el número correctamente -->
                            <input type="hidden" name="novela_id" value="<?php echo htmlspecialchars($id_novela); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Título Del Capítulo (opcional)</label>
                            <input type="text" name="titulo_cap" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($titulo_cap ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Número de Capítulo</label>
                            <input type="number" name="numero" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars((string) ($numero ?? '')); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Texto</label>
                            <textarea name="contenido" class="form-control bg-secondary text-white border-0"
                                rows="3"><?php echo htmlspecialchars($contenido ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-25">Subir Capítulo</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
