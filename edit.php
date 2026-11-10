<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar: <?php echo $novel['titulo']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: #121212; color: #fff; padding: 20px;">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-white p-4 border-secondary">
                    <a class="btn btn-outline-light mb-3" href="ver_novela.php?id=<?php echo $novel['id_novela']; ?>"
                        role="button">← Volver</a>

                    <h3 class="mb-4 text-center">Gestión de Novela</h3>

                    <!-- El formulario se envía a esta misma página -->
                    <form id="formNovela" action="index.php?action=update" method="POST">
                        <input type="hidden" name="novela_id" value="<?php echo $novel['id_novela']; ?>">

                        <div class="mb-3">
                            <label class="form-label text-white">Título de la Novela</label>
                            <!-- Imprimimos el valor actual en el atributo 'value' -->
                            <input type="text" name="titulo" class="form-control bg-secondary text-white border-0"
                                value="<?php echo $novel['titulo']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">URL de la Portada</label>
                            <input type="text" id="urlPortada" name="portada"
                                class="form-control bg-secondary text-white border-0"
                                value="<?php echo $novel['portada']; ?>" required>
                            <div id="errorPortada" class="text-danger mt-1" style="display: none; font-size: 0.85em;">
                                ¡Error! La URL de la portada debe empezar con http:// o https://
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Estado </label>
                            <select name="estado" class="form-control">
                                <option value="en emision" <?php echo ($novel['estado'] == 'en emision') ? 'selected' : ''; ?>>En emisión</option>
                                <option value="finalizado" <?php echo ($novel['estado'] == 'finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                                <option value="abandonado" <?php echo ($novel['estado'] == 'abandonado') ? 'selected' : ''; ?>>Abandonado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white"> Genero </label>
                            <input type="text" name="genero" class="form-control bg-secondary text-white border-0"
                                value="<?php echo $novel['genero']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white"> Autor </label>
                            <input type="text" name="autor" class="form-control bg-secondary text-white border-0"
                                value="<?php echo $novel['autor']; ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white">Sinopsis</label>
                            <!-- Los textareas no usan 'value', el texto va entre las etiquetas -->
                            <textarea name="sinopsis" class="form-control bg-secondary text-white border-0" rows="5"
                                required><?php echo $novel['sinopsis']; ?></textarea>
                        </div>
                        <div class="d-flex gap-3 mt-4">

                            <!-- Botón para Editar (el cierre de tu primer form) -->
                            <button type="submit" name="accion" value="editar" class="btn btn-primary px-4">
                                Guardar Cambios
                            </button>
                    </form>

                    <!-- Formulario para Eliminar -->
                    <form action="index.php?action=delete" method="POST" class="m-0">
                        <!-- OJO: Te corregí $novel por $novela para que no te tire error de variable -->
                        <input type="hidden" name="novela_id" value="<?php echo $novel['id_novela']; ?>">
                        <button type="submit" class="btn btn-danger px-4"
                            onclick="return confirm('¿Estás seguro de que querés borrar esta novela entera? Esto no se puede deshacer.');">
                            Eliminar Novela
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <script src="validaciones.js"></script>
</body>

</html>