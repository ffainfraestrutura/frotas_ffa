<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Redirecionando com localiza��o</title>
</head>
<body>
  <h2>Obtendo sua localiza��o...</h2>

  <script>
    // Verifica se o navegador suporta geolocaliza��o
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          const lat = position.coords.latitude;
          const lon = position.coords.longitude;

          // Monta a URL com os par�metros GET
          const url = `https://ffa.painel-telecom.com:4242/frotas/toaqui.php?latitude=${lat}&longitude=${lon}`;

          // Redireciona
          window.location.href = url;
        },
        function (error) {
          alert("Erro ao obter localiza��o: " + error.message);
        }
      );
    } else {
      alert("Geolocaliza��o n�o � suportada pelo seu navegador.");
    }
  </script>
</body>
</html>
