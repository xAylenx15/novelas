<!DOCTYPE html>
<html>

<head>
    <title>Subir Novela</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/style2.css" rel="stylesheet">
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

                    <form id="formNovela" action="/index.php?action=store" method="POST">
                        <a class="btn btn-outline-light mb-3" href="../../index.php">← Volver</a>

                        <div class="mb-3">
                            <label class="form-label text-white">Título</label>
                            <input type="text" name="titulo" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($titulo ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Enlace de la portada</label>
                            <input type="text" id="urlPortada" name="portada"
                                class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($portada ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Estado</label>
                            <select name="estado" class="form-control bg-secondary text-white border-0">
                                <option value="">Seleccione un estado...</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?php echo $est['id_estado']; ?>" <?php echo (($estado ?? '') == $est['id_estado']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($est['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Género</label>
                            <select name="genero" class="form-control bg-secondary text-white border-0">
                                <option value="">Seleccione un género...</option>
                                <?php foreach ($generos as $gen): ?>
                                    <option value="<?php echo $gen['id_genero']; ?>" <?php echo (($genero ?? '') == $gen['id_genero']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($gen['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Autor</label>
                            <input type="text" name="autor" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($autor ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Sinopsis</label>
                            <textarea name="sinopsis" class="form-control bg-secondary text-white border-0"
                                rows="4"><?php echo htmlspecialchars($sinopsis ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">Subir Novela</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
