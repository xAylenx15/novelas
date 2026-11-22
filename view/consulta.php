<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Novelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .miniatura-portada {
            width: 55px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .miniatura-portada:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.25);
        }
        .sinopsis-corta {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help;
        }
    </style>
</head>
<body class="bg-dark text-white">

<div class="container py-4">
    <!-- Cabecera -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php?action=index" class="btn btn-outline-light">← Volver al inicio</a>
        <h2 class="mb-0">Consultar Novelas</h2>
        <div style="width: 140px;"></div> <!-- Espaciador para mantener el título centrado -->
    </div>

    <!-- Formulario de Filtros -->
    <div class="card bg-secondary bg-opacity-25 border-secondary mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="action" value="consulta">

                <div class="col-md-3">
                    <label for="titulo" class="form-label text-white">Título</label>
                    <input type="text" id="titulo" name="titulo" 
                           class="form-control bg-dark text-white border-secondary"
                           placeholder="Ej: El príncipe..."
                           value="<?php echo htmlspecialchars($_GET['titulo'] ?? ''); ?>">
                </div>

                <div class="col-md-3">
                    <label for="autor" class="form-label text-white">Autor</label>
                    <input type="text" id="autor" name="autor" 
                           class="form-control bg-dark text-white border-secondary"
                           placeholder="Ej: Desconocido..."
                           value="<?php echo htmlspecialchars($_GET['autor'] ?? ''); ?>">
                </div>

                <div class="col-md-2">
                    <label for="genero" class="form-label text-white">Género</label>
                    <select id="genero" name="genero" class="form-select bg-dark text-white border-secondary">
                        <option value="">Todos</option>
                        <?php if (!empty($generos)): ?>
                            <?php foreach ($generos as $g): ?>
                                <option value="<?php echo $g['id_genero']; ?>"
                                    <?php echo (isset($_GET['genero']) && $_GET['genero'] == $g['id_genero']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['nombre'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="estado" class="form-label text-white">Estado</label>
                    <select id="estado" name="estado" class="form-select bg-dark text-white border-secondary">
                        <option value="">Todos</option>
                        <?php if (!empty($estados)): ?>
                            <?php foreach ($estados as $e): ?>
                                <option value="<?php echo $e['id_estado']; ?>"
                                    <?php echo (isset($_GET['estado']) && $_GET['estado'] == $e['id_estado']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['nombre'] ?? ''); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    <a href="index.php?action=consulta" class="btn btn-outline-light" title="Limpiar filtros">✕</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="table-responsive">
        <table class="table table-dark table-hover table-bordered border-secondary align-middle">
            <thead class="table-secondary text-dark">
                <tr>
                    <th class="text-center">Portada</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Género</th>
                    <th>Estado</th>
                    <th class="text-center">Capítulos</th>
                    <th>Sinopsis</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($novelas)): ?>
                    <?php foreach ($novelas as $novela): ?>
                        <tr>
                            <td class="text-center">
                                <a href="index.php?action=showNovela&id=<?php echo $novela['id_novela']; ?>" title="Ver novela">
                                    <img src="<?php echo htmlspecialchars($novela['portada'] ?? ''); ?>" 
                                         alt="Portada de <?php echo htmlspecialchars($novela['titulo'] ?? ''); ?>" 
                                         class="miniatura-portada">
                                </a>
                            </td>
                            <td class="fw-bold">
                                <?php echo htmlspecialchars($novela['titulo'] ?? ''); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($novela['autor'] ?? 'Desconocido'); ?>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?php echo htmlspecialchars($novela['genero'] ?? ''); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($novela['estado'] ?? ''); ?>
                                </span>
                            </td>
                            <td class="text-center fw-semibold">
                                <?php echo (int)($novela['cantidad_capitulos'] ?? 0); ?>
                            </td>
                            <td>
                                <div class="sinopsis-corta" title="<?php echo htmlspecialchars($novela['sinopsis'] ?? 'Sin sinopsis'); ?>">
                                    <?php echo htmlspecialchars($novela['sinopsis'] ?? 'Sin sinopsis'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No se encontraron novelas con los filtros seleccionados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>