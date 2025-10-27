<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Club Fotografía / Video</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f5f7fa, #d7e1ec);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 40px 20px;
    }

    header {
      width: 100%;
      max-width: 800px;
      text-align: center;
      margin-bottom: 30px;
    }

    header h1 {
      color: #7a1c3a;
      font-size: 28px;
      margin-bottom: 10px;
    }

    header a {
      text-decoration: none;
      color: #007bff;
      font-size: 14px;
    }

    .club-content {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 30px;
      max-width: 800px;
      width: 100%;
    }

    .club-content img {
      width: 100%;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .club-content p {
      font-size: 16px;
      margin: 10px 0;
    }

    .club-content strong {
      color: #7a1c3a;
    }

    .btn-registrar {
      display: inline-block;
      background: #7a1c3a;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      margin-top: 20px;
    }

    .btn-registrar:hover {
      background: #5e142c;
    }
  </style>
</head>
<body>

  <header>
    <h1>Club de Fotografía / Video</h1>
    <a href="../../club.php?type=cultural">← Volver a la lista de clubes culturales</a>
  </header>

  <div class="club-content">
     <img src="../assets/images/banda9.jpg" alt="Imagen Banda de Guerra">
    <p><strong>Profesor responsable:</strong> Mtro. Carlos López</p>
    <p><strong>Descripción:</strong> Aprende técnicas de fotografía, edición y producción audiovisual. Participa en proyectos creativos, exposiciones escolares y colaboraciones multimedia.</p>
    <p><strong>Actividades destacadas:</strong> Talleres de cámara, edición digital, salidas fotográficas, producción de cortos.</p>
    <a class="btn-registrar" href="../../register_club.php?type=cultural&club=Fotografía/Video">Registrarme</a>
  </div>

</body>
</html>
