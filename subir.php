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

                    <!-- Ruta corregida para que el controlador lo procese desde la raíz -->
                    <form action="../../index.php?action=storeCapitulo" method="POST">
                        <!-- Ruta corregida para el botón volver -->
                        <a class="btn btn-outline-light mb-3" href="../../index.php" role="button"> ← Volver</a>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Novela ID</label>
                            <input type="number" name="novela_id" class="form-control bg-secondary text-white border-0" required>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-white">Titulo Del Capitulo</label>
                            <input type="text" name="titulo_cap" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Numero de Capitulo</label>
                            <input type="number" name="numero" class="form-control bg-secondary text-white border-0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Texto</label>
                            <textarea name="contenido" class="form-control bg-secondary text-white border-0"
                                rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-25">Subir Capitulo</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>