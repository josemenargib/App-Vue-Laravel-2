<!doctype html>
<html lang="en">
<head>
    <title>Prueba Técnica</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
</head>
<body style="text-align:center; font-family: Arial, sans-serif;">
    <h1>{{ $tipo }}</h1>

    <p>{{ $mensaje }}</p>

    <!-- Botón con el enlace dinámico -->
    <a href="{{ $enlacePrueba }}" style="text-decoration: none;">
        <button style="background-color: rgb(11, 193, 238); color: white; padding: 15px 30px; font-size: 18px; border: none; border-radius: 5px; cursor: pointer; margin: 20px 0; cursor: pointer;">
            Click aquí
        </button>
    </a>

    <!-- Enlace alternativo -->
    <h4>Alternativamente, también puede visitar:</h4>
    <a href="{{ $enlacePrueba }}" style="font-family: Arial, sans-serif;">Click aquí</a>
</body>
</html>

