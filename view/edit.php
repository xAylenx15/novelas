<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar: <?php echo htmlspecialchars($novel['titulo']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #121212; color: #fff; padding: 20px;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-white p-4 border-secondary">
                    <a class="btn btn-outline-light mb-3" href="index.php?action=showNovela&id=<?php echo $novel['id_novela']; ?>"
                        role="button">← Volver</a>

                    <h3 class="mb-4 text-center">Gestión de Novela</h3>

                    <!-- ACÁ SE MUESTRA EL ERROR QUE MANDA POSTGRESQL -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Atención:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form id="formNovela" action="index.php?action=update" method="POST">
                        <input type="hidden" name="novela_id" value="<?php echo $novel['id_novela']; ?>">

                        <!-- NOTA: Se borraron todos los 'required' a propósito -->
                        <div class="mb-3">
                            <label class="form-label text-white">Título de la Novela</label>
                            <input type="text" name="titulo" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($novel['titulo']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">URL de la Portada</label>
                            <input type="text" id="urlPortada" name="portada"
                                class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($novel['portada']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Estado</label>
                            <select name="estado" class="form-control bg-secondary text-white border-0">
                                <option value="">Seleccione...</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?php echo $est['id_estado']; ?>" 
                                        <?php echo ($novel['id_estado'] == $est['id_estado']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($est['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                       
                        <div class="mb-3">
    <label class="form-label text-white">Género</label>
    
    <!-- Acá arranca la lista desplegable (<select>) -->
    <select name="genero" class="form-control bg-secondary text-white border-0">
        <option value="">Seleccione...</option>
        
        <!-- El bucle foreach dibuja una <option> por cada género en la base de datos -->
        <?php foreach ($generos as $gen): ?>
            <option value="<?php echo $gen['id_genero']; ?>" 
                <?php echo ($novel['id_genero'] == $gen['id_genero']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($gen['nombre']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

                        <div class="mb-3">
                            <label class="form-label text-white">Autor</label>
                            <input type="text" name="autor" class="form-control bg-secondary text-white border-0"
                                value="<?php echo htmlspecialchars($novel['autor']); ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white">Sinopsis</label>
                            <textarea name="sinopsis" class="form-control bg-secondary text-white border-0" rows="5"
                                ><?php echo htmlspecialchars($novel['sinopsis']); ?></textarea>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" name="accion" value="editar" class="btn btn-primary px-4 w-100">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>

                    <form action="index.php?action=delete" method="POST" class="mt-3">
                        <input type="hidden" name="novela_id" value="<?php echo $novel['id_novela']; ?>">
                        <button type="submit" class="btn btn-danger px-4 w-100"
                            onclick="return confirm('¿Estás seguro de que querés borrar esta novela entera? Esto no se puede deshacer.');">
                            Eliminar Novela
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
