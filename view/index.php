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
            position: relative;
            background: linear-gradient(to right, #111, #222);
            border-bottom: 1px solid #444;
            border-radius: 0 0 20px 20px;
            padding: 3.5rem 1rem 3rem;
            margin-bottom: 3rem;
        }

        /* Lupa sola arriba a la derecha */
        .icono-lupa {
            position: absolute;
            top: 20px;
            right: 25px;
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .icono-lupa:hover {
            color: #ffffff;
            transform: scale(1.15);
        }
    </style>
</head>

<body style="background-color: #121212; color: #fff;">
    <div class="container pb-5">

        <!-- Cabecera de la página -->
        <div class="biblioteca-header text-center shadow-lg">

            <!-- Lupa sola arriba a la derecha -->
            <a href="index.php?action=consulta" class="icono-lupa"
               title="Consultar novelas" aria-label="Consultar novelas">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                </svg>
            </a>

            <h1 class="display-5 fw-bold mb-2">Mi biblioteca de traducciones</h1>

            <p class="lead text-secondary mb-4">Listado de novelas traducidas</p>

            <a href="index.php?action=create" class="btn btn-outline-light btn-lg px-4 rounded-pill fw-bold">
                + Subir Nueva Novela
            </a>
        </div>

        <!-- Fila con espaciado g-4 (gap) para separar las tarjetas -->
        <div class="row g-4">
            <?php foreach ($novel as $row): ?>

                <div class="col-12 col-sm-6 col-md-4 col-lg-3">

                    <div class="card bg-dark text-white border-secondary h-100 d-flex flex-column card-novela">

                        <img src="<?php echo htmlspecialchars($row['portada']); ?>" class="portada-novela"
                            alt="Portada de <?php echo htmlspecialchars($row['titulo']); ?>">

                        <div class="card-body d-flex flex-column p-3">
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
