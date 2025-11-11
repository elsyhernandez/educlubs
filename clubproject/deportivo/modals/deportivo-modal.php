<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Club</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(to bottom, #7a1c3a, #2c2c2c);
      color: white;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.6);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      border-radius: 10px;
      position: relative;
      color: #333;
    }
    .close-button {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .close-button:hover,
    .close-button:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .modal-iframe {
      width: 100%;
      height: 400px;
      border: none;
    }
  </style>
</head>
<body>
<div id="registroModal" class="modal">
  <div class="modal-content">
    <span class="close-button" onclick="closeModal()">&times;</span>
    <iframe id="modalIframe" class="modal-iframe" src="" frameborder="0"></iframe>
  </div>
</div>

<script>
  function openModal(url) {
    document.getElementById('modalIframe').src = url;
    document.getElementById('registroModal').style.display = "block";
  }

  function closeModal() {
    document.getElementById('registroModal').style.display = "none";
    document.getElementById('modalIframe').src = "";
  }

  window.onclick = function(event) {
    if (event.target == document.getElementById('registroModal')) {
      closeModal();
    }
  }
</script>
</body>
</html>
