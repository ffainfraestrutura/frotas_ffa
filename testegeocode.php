<!DOCTYPE html>
<html>
<head>
    <title>Obter Geolocalização</title>
</head>
<body>
    <button onclick="getLocation()">Obter Localização</button>
    <p id="demo"></p>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                document.getElementById("demo").innerHTML = "Geolocalização não suportada pelo seu navegador.";
            }
        }

        function showPosition(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            document.getElementById("demo").innerHTML = "Latitude: " + latitude + "<br>Longitude: " + longitude;
        }
    </script>
</body>
</html>
