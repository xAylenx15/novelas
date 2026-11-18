<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Novelas - Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style2.css" rel="stylesheet">

    <style>
        /* Efecto hover para que las tarjetas floten al pasar el mouse */
        .card-novela {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            /* Mantiene la imagen adentro de los bordes redondeados */
        }

        .card-novela:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1) !important;
            border-color: #6c757d !important;
        }

        /* Portadas perfectas y simétricas */
        .portada-novela {
            height: 320px;
            width: 100%;
            object-fit: cover;
            border-bottom: 2px solid #333;
        }

        /* Estilo del Header principal */
        .biblioteca-header {
            background: linear-gradient(to right, #111, #222);
            border-bottom: 1px solid #444;
            border-radius: 0 0 20px 20px;
            padding: 3rem 1rem;
            margin-bottom: 3rem;
        }
    </style>
</head>

<body style="background-color: #121212; color: #fff;">
    <div class="container pb-5">

        <!-- Cabecera de la página mejorada -->
        <div class="biblioteca-header text-center shadow-lg">
            <h1 class="display-5 fw-bold mb-2">Mi biblioteca de traducciones</h1>
            <p class="lead text-secondary mb-4">Listado de novelas traducidas</p>

            <!-- Ruta corregida hacia el controlador -->
            <a href="index.php?action=create" class="btn btn-outline-light btn-lg px-4 rounded-pill fw-bold">
                + Subir Nueva Novela
            </a>
        </div>

        <!-- Fila con espaciado g-4 (gap) para separar las tarjetas -->
        <div class="row g-4">
            <?php foreach ($novel as $row): ?>

                <!-- Clases responsivas: 1 columna en móvil, 2 en tablet, 3-4 en PC -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                    <!-- Tarjeta con la clase card-novela para el hover -->
                    <div class="card bg-dark text-white border-secondary h-100 d-flex flex-column card-novela">

                        <!-- Imagen borde a borde -->
                        <img src="<?php echo htmlspecialchars($row['portada']); ?>" class="portada-novela"
                            alt="Portada de <?php echo htmlspecialchars($row['titulo']); ?>">

                        <!-- Cuerpo con el texto y el botón -->
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Reemplazamos 'text-truncate' por 'titulo-novela' -->
                            <h5 class="card-title fw-bold titulo-novela"
                                title="<?php echo htmlspecialchars($row['titulo']); ?>">
                                <?php echo htmlspecialchars($row['titulo']); ?>
                            </h5>


                            <p class="card-text text-secondary small mb-4">
                                <?php
                                $sinopsis = $row['sinopsis'] ?? '';
                                echo htmlspecialchars(mb_substr($sinopsis, 0, 110)) . (mb_strlen($sinopsis) > 110 ? '...' : '');
                                ?>
                            </p>

                            <!-- Cambiamos 'btn-primary' por nuestra clase 'btn-beige' -->
                            <a href="/index.php?action=showNovela&id=<?php echo $row['id_novela']; ?>"
                                class="btn btn-beige w-100 mt-auto fw-semibold">Ver novela</a>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>
