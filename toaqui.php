<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;




$latitude = isset($_GET["latitude"]) ? $_GET["latitude"] : "N/A";
$longitude = isset($_GET["longitude"]) ? $_GET["longitude"] : "N/A";


include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");




?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

</head>
<?php
// Consulta o centro de custo do funcionário com base na matrícula
$sqlaux = "SELECT ccusto, uf_trabalho FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
$resultadoaux = mysql_query($sqlaux) or die(mysql_error());
$rowaux = mysql_fetch_array($resultadoaux);
$ccusto = $rowaux[0];
$uftrab = $rowaux[1];
// Verifica a UF e se contém "Claro" no centro de custo
if ($uftrab == 'RJ') {
    if (stripos($ccusto, 'Claro') !== false) {
        $uftrab_classificado = 'RJC';
    } else {
        $uftrab_classificado = 'RJI';
    }
} else {
    $uftrab_classificado = $uftrab; // Mantém a UF original para outros estados
}
// Define os centros de custo específicos
$centrosExatos = ['1', '2', '31', '44', '57', '60', '62', '64', '67', '76', '77'];

// Verifica se o ccusto corresponde aos critérios
$mostrarOptionAtividade = false;
$mostrarOptionBase = false;
$mostrarOptionMatriz = false;

// ========================================
// LÓGICA ORIGINAL - RESTRIÇÕES POR CCUSTO (COMENTADA):
// Apenas centros de custo específicos podiam usar "Atividade" sem restrição geográfica
// Outros centros de custo precisavam estar próximos a Base/Matriz
// ========================================
/*
// Remove espaços extras e faz a verificação case-insensitive
if (
    stripos(trim($ccusto), 'rede externa') !== false ||
    stripos(trim($ccusto), 'implanta') !== false ||
    stripos(trim($ccusto), 'obra') !== false ||
    in_array(trim($ccusto), $centrosExatos) // Verifica se está na lista dos valores exatos
) {
    $mostrarOptionAtividade = true;
}
*/

// ========================================
// NOVA LÓGICA (ATIVA) - VERSÃO LIBERADA:
// TODOS os centros de custo podem usar "Base", "Matriz" e "Atividade"
// Seguindo as mesmas regras da "Rede Externa":
// - Atividade: SEM restrição geográfica (de qualquer lugar)
// - Base: Apenas se ≤330m de uma base
// - Matriz: Apenas se ≤330m da matriz
// ========================================
// Agora qualquer técnico pode registrar "Atividade" de qualquer lugar
$mostrarOptionAtividade = true;

//echo "<br><br><br><br><br>";
//echo "<p>Centro de Custo: " . htmlspecialchars($ccusto) . "</p>";


// Verifica a distância do usuário para as bases
function haversine($lat1, $lon1, $lat2, $lon2)
{
    $R = 6371; // Raio da Terra em km
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($lat1) * cos($lat2) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c * 1000; // Retorna a distância em metros
}

// Coordenadas do usuário
$latitudeUsuario = $latitude; // Valor vindo do form
$longitudeUsuario = $longitude;

// Consulta ao banco de dados
$sqlBases = "SELECT latitude, longitude, codempresa, codfilial FROM bdaniel.tbfilial";
$resultadoBases = mysql_query($sqlBases) or die(mysql_error());

// Já inicializadas acima como false
// $mostrarOptionBase = false;
// $mostrarOptionMatriz = false;
//echo "<br><br><br><br><br>";
while ($row = mysql_fetch_assoc($resultadoBases)) {
    $latitudeBase = $row['latitude'];
    $longitudeBase = $row['longitude'];
    $codempresa = $row['codempresa'];
    $codfilial = $row['codfilial'];

    // Mostrar dados na tela para depuração

    //echo "<p>Base: Latitude=$latitudeBase, Longitude=$longitudeBase, CodEmpresa=$codempresa, CodFilial=$codfilial</p>";

    // Calcula a distância
    $distancia = haversine($latitudeUsuario, $longitudeUsuario, $latitudeBase, $longitudeBase);

    // Mostrar distância calculada
    //echo "<p>Distância para base: $distancia metros</p>";

    // Verifica se está dentro do raio de 100 metros
    if ($distancia <= 330) {
        $mostrarOptionBase = true;
        //echo "<p>Base dentro do raio: Latitude=$latitudeBase, Longitude=$longitudeBase</p>";

        // Verifica se é matriz (codempresa e codfilial = 1)
        if ((int) $codempresa === 1 && (int) $codfilial === 1) {
            $mostrarOptionMatriz = true;
            //echo "<p>Matriz detectada: CodEmpresa=$codempresa, CodFilial=$codfilial</p>";
        }
    }
}

// Verifica elegibilidade do usuário
$mostrarMensagemNaoElegivel = !$mostrarOptionBase && !$mostrarOptionMatriz && !$mostrarOptionAtividade;
?>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 pt-1"> Tô aqui! </h1>

                <p class="fs-5 fw-bold">Técnico: <?php echo $nome; ?></p>

                <?php if ($mostrarMensagemNaoElegivel): ?>
                    <div class="alert alert-danger" role="alert">
                        Você não está elegível para nenhuma das opções disponíveis.
                    </div>
                    <div class="mt-4">
                        <a href="./tecnico.php" class="btn btn-primary">Voltar</a>
                    </div>
                <?php else: ?>
                    <form action="./control/toaqui.php" method="post" enctype='multipart/form-data'
                        onsubmit="return validarMotivo();">
                        <!-- div solicitação -->
                        <div>
                            <label class="form-label mb-0 fs-5" for="motivo">Motivo:</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Motivo</span>
                                <select class="form-select" name="motivo" id="motivo" required>
                                    <option value="">Selecione o motivo</option>
                                    <?php if ($mostrarOptionBase): ?>
                                        <option value="base">Base</option>
                                    <?php endif; ?>
                                    <?php if ($mostrarOptionMatriz): ?>
                                        <option value="matriz">Matriz</option>
                                    <?php endif; ?>
                                    <?php if ($mostrarOptionAtividade): ?>
                                        <option value="atividade">Atividade</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" value="<?php echo $latitude; ?>" name="latitude" />
                        <input type="hidden" value="<?php echo $longitude; ?>" name="longitude" />

                        <!-- div justificativa -->
                        <div class="mb-3">
                            <label class="form-label mb-0 fs-5" for="ijust">Observação:</label>
                            <textarea step="0.01" rows="10" class="form-control" name="justificativa"
                                placeholder="Escreva a justificativa"></textarea>
                        </div>

                        <?php echo "<input type='hidden' name='matricula' value='$matricula'>"; ?>
                        <?php echo "<input type='hidden' name='uftrab' value='$uftrab_classificado'>"; ?>

                        <!-- div botão confirmar -->
                        <div class="mt-4" id='inputGroupFileAddon04'>
                            <button class='btn btn-success' type='submit'>Confirmar</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>




        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                    <a href="#">Privacy Policy</a>
                    &middot;
                    <a href="#">Terms &amp; Conditions</a>
                </div>!-->
                </div>
            </div>
        </footer>
    </div>
    <script>
        let formSubmitted = false;

        function validarMotivo() {
            if (formSubmitted) {
                return false;
            }

            const motivo = document.getElementById('motivo').value;
            const submitButton = document.querySelector('button[type="submit"]');

            // Desabilita o botão imediatamente
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';

            if (motivo === "") {
                alert("Por favor, selecione um motivo antes de enviar.");
                submitButton.disabled = false;
                submitButton.textContent = 'Confirmar';
                return false;
            }

            formSubmitted = true;
            return true;
        }
    </script>

    <script>
        //função para formatar
        function formatarMoeda() {
            var elemento = document.getElementById('ivalor');
            var valor = elemento.value;


            valor = valor + '';
            valor = parseInt(valor.replace(/[\D]+/g, ''));
            valor = valor + '';
            valor = valor.replace(/([0-9]{2})$/g, ",$1");

            if (valor.length > 6) {
                valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
            }

            elemento.value = valor;
            if (valor == 'NaN') elemento.value = '';

        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>