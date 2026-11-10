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

                    <form id="formNovela" action="/index.php?action=store" method="POST">
                        <a class="btn btn-outline-light mb-3" href="../../index.php">← Volver</a>
                        <div class="mb-3">
                            <label class="form-label text-white">Titulo</label>
                            <input type="text" name="titulo" class="form-control bg-secondary text-white border-0"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Enlace de la portada</label>
                            <input type="text" id="urlPortada" name="portada"
                                class="form-control bg-secondary text-white border-0" required>
                            <div id="errorPortada" class="text-danger mt-1" style="display: none; font-size: 0.85em;">
                                ¡Error! La URL de la portada debe empezar con http:// o https://
                            </div>
                        </div>
                       
                       <div class="mb-3"> 
                         <label class="form-label text-white">Estado </label>
                             <select name="estado" class="form-control">
                            <option value="en emision">En emisión</option>
                            <option value="finalizado">Finalizado</option>
                            <option value="abandonado">Abandonado</option>
                        </select>
                       </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Genero</label>
                            <input name="genero" class="form-control bg-secondary text-white border-0" required>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-white">Autor</label>
                            <input name="autor" class="form-control bg-secondary text-white border-0"
                                required>
                        </div>
                         <div class="mb-3">
                            <label class="form-label text-white">Sipnosis</label>
                            <textarea name="sinopsis" class="form-control bg-secondary text-white border-0" rows="4"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">Subir Novela</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script src="/validaciones.js"></script>
</body>

</html>