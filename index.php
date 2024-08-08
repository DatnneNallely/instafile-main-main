<?php
$carpetaNombre = isset($_GET['-']) ? $_GET['-'] : '';
$carpetaNombreCorta = substr($carpetaNombre, 0, 3);
$carpetaRuta = "./descarga/" . $carpetaNombre;

try {
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
        $mensaje = "Carpeta '$carpetaNombre' creada con éxito.";
    } else {
        $mensaje = "La carpeta '$carpetaNombre' ya existe.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivo'])) {
            foreach ($_FILES['archivo']['tmp_name'] as $key => $tmp_name) {
                $archivoNombre = $_FILES['archivo']['name'][$key];
                $archivoNombre = str_replace(' ', '_', $archivoNombre);
                $archivoTmp = $_FILES['archivo']['tmp_name'][$key];
                
                if (move_uploaded_file($archivoTmp, $carpetaRuta . '/' . $archivoNombre)) {
                    $subido = true;
                    $mensaje = "Archivo(s) subido(s) con éxito.";
                } else {
                    throw new Exception("Error al subir el archivo.");
                }
            }
        }
    }

    if (isset($_POST['eliminarArchivo'])) {
        $archivoAEliminar = $_POST['eliminarArchivo'];
        $archivoRutaAEliminar = $carpetaRuta . '/' . $archivoAEliminar;

        if (file_exists($archivoRutaAEliminar)) {
            if (unlink($archivoRutaAEliminar)) {
                $mensaje = "Archivo '$archivoAEliminar' eliminado con éxito.";
            } else {
                throw new Exception("Error al eliminar el archivo.");
            }
        } else {
            throw new Exception("El archivo '$archivoAEliminar' no existe.");
        }
    }
} catch (Exception $e) {
    $mensaje = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir Archivos</title>
    <script src="parametro.js"></script>
    <link rel="stylesheet" href="estilo.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #0730c5;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        h1 {
            font-size: 2.5em;
            text-align: center;
            margin: 20px 0;
        }

        h3 {
            font-size: 1.2em;
            color: #0540a4;
            text-align: center;
        }

        .drop-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 90%;
            max-width: 600px;
            height: 200px;
            border: 2px dashed #0730c5;
            border-radius: 20px;
            cursor: pointer;
            position: relative;
            text-align: center;
            margin: 20px auto;
        }

        .file-input {
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            position: absolute;
            top: 0;
            left: 0;
        }

        .drop-area svg {
            width: 50px;
            height: 50px;
            fill: #0730c5;
        }

        .drop-area p {
            font-size: 18px;
            color: #0730c5;
            margin-top: 20px;
        }

        .container2 {
            margin-top: 20px;
        }

        .archivos_subidos {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #0730c5;
            border-radius: 10px;
            background-color: #f0f8ff;
            width: 90%;
            max-width: 600px;
            margin: 10px auto;
        }

        .archivos_subidos a {
            text-decoration: none;
            color: #0540a4;
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
        }

        .btn_delete {
            background-color: transparent;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn_delete svg {
            fill: #0730c5;
        }

        .link-container {
            margin-top: 20px;
            text-align: center;
        }

        .link-container button {
            background-color: #0730c5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

        .link-container button:hover {
            background-color: #0540a4;
        }

        .credits {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .beta {
            font-size: 0.6em;
            background-color: #0730c5;
            color: white;
            padding: 3px 7px;
            border-radius: 5px;
            vertical-align: super;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .drop-area {
                height: 150px;
                font-size: 16px;
            }

            .archivos_subidos {
                width: 95%;
                padding: 5px;
            }

            .archivos_subidos a {
                font-size: 14px;
            }

            .link-container button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.8em;
            }

            h3 {
                font-size: 1em;
            }

            .drop-area {
                height: 120px;
                font-size: 14px;
            }

            .archivos_subidos {
                padding: 5px;
            }

            .archivos_subidos a {
                font-size: 12px;
            }

            .link-container button {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>
</head>

<body>
    <h1>Compartir Archivos<sup class="beta">BETA</sup></h1>
    <div class="content">
        <h3>Sube tus archivos y comparte este enlace temporal: <span>datnne.online/?-=<?php echo htmlspecialchars($carpetaNombreCorta); ?></span></h3>
        <div class="link-container">
            <button onclick="copiarAlPortapapeles()">Copiar enlace</button>
        </div>
        <div class="container">
            <div class="drop-area" id="drop-area">
                <form action="" id="form" method="POST" enctype="multipart/form-data">
                    <input type="file" class="file-input" name="archivo[]" id="archivo" multiple onchange="document.getElementById('form').submit()">
                    <label for="archivo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M13 19v-4h3l-4-5-4 5h3v4z"></path>
                            <path d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"></path>
                        </svg>
                    </label>
                    <p>Arrastra tus archivos aquí</p>
                </form>
            </div>

            <div class="container2">
                <div id="file-list" class="pila">
                    <?php
                    $targetDir = $carpetaRuta;
                    $files = scandir($targetDir);
                    $files = array_diff($files, array('.', '..'));

                    if (count($files) > 0) {
                        echo "<h3 style='margin-bottom:10px;'>Archivos Subidos:</h3>";

                        foreach ($files as $file) {
                            echo "<div class='archivos_subidos'>
                            <div><a href='$carpetaRuta/$file' download class='boton-descargar'>$file</a></div>
                            <div>
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminarArchivo' value='$file'>
                                <button type='submit' class='btn_delete'>
                                    <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                        <path d='M4 7l16 0' />
                                        <path d='M10 11l0 6' />
                                        <path d='M14 11l0 6' />
                                        <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />
                                        <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        </div>";
                        }
                    } else {
                        echo "No se han subido archivos.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="credits">
        <p>© Copyright Hervias Dat 2024</p>
    </div>

    <script>
        function copiarAlPortapapeles() {
            const enlace = "datnne.online/?-=<?php echo htmlspecialchars($carpetaNombreCorta); ?>";
            navigator.clipboard.writeText(enlace).then(() => {
                alert('Enlace copiado al portapapeles!');
            }).catch(err => {
                console.error('Error al copiar el enlace: ', err);
            });
        }
    </script>
</body>

</html>
