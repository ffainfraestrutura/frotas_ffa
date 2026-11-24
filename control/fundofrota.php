<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$hoje = date('Y-m-d H:i:s');
$ultdom = date('Y-m-d', strtotime($data . 'last sunday')); //última domingo

include"../conecta.php";
include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

$valor = $_POST['valor'];
if(strpos($valor, ".") !== false){
    $valor = str_replace(".","",$valor);
}

$valor = str_replace(",",".",$valor);

$saldofrota = $_POST['saldofrota'];
$saldofrota = str_replace(",",".",$saldofrota);


$cargof = $_POST['cargof'];
$matpesq = $_POST['matpesq'];
$mat_autor = $matricula;

//print "$saldofrota, $valor, $cargof, $matpesq, $mat_autor<br>";

if($valor>$saldofrota){
    //echo"<script language='javascript' type='text/javascript'>alert('Saldo insuficiente!');window.location=\"../fundofrota.php\"</script>";
    if($cargof=='superv'){
        echo "
        <head>
            
            <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
            <script type='text/javascript'>
                $(document).ready(function() {                
                    
                    alert('Saldo insuficiente!');
                
                    // monta o formulário
                    
                    var formulario = \"<form id='form' hidden method='post' action='../fundofrota.php'>\"
                    +\"<input name='supervisor' value='$matpesq'>\"
                    +\"<input name='cargo' value='superv'>\"                       
                    +\"</form>\";
        
                    // insere o formulário no body
                    $('body').append(formulario);
                    // faz o submit e remove o formulário
                    $('#form').submit().remove();

                });
            </script>
        </head>";
    }elseif($cargof=='coord'){
        echo "
        <head>
            
            <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
            <script type='text/javascript'>
                $(document).ready(function() {                
                    
                    alert('Saldo insuficiente!');
                
                    // monta o formulário
                    
                    var formulario = \"<form id='form' hidden method='post' action='../fundofrota.php'>\"
                    +\"<input name='coordenador' value='$matpesq'>\"
                    +\"<input name='cargo' value='coord'>\"                       
                    +\"</form>\";
        
                    // insere o formulário no body
                    $('body').append(formulario);
                    // faz o submit e remove o formulário
                    $('#form').submit().remove();

                });
            </script>
        </head>";
    }

}else{
    if($cargof=='superv'){
        //selecionar tudo que ele já recebu na semana, totorccomp
        $sql="SELECT valor, totorccomp FROM bdfrota.tbsupervisor WHERE matricula='$matpesq'; ";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
            $valorsuperv = $row[0];
            $totorccomp = $row[1];    
        
        //print"$valorsuperv, $totorccomp, $valor<br>";
        
        $novosaldosup = $valorsuperv + $valor;
        $novototorccomp = $totorccomp + $valor;

        if(strpos($novosaldosup, ",") !== false){
            $novosaldosup = str_replace(",",".",$novosaldosup);
        }

        if(strpos($novototorccomp, ",") !== false){
            $novototorccomp = str_replace(",",".",$novototorccomp);
        }

        //print "$novosaldosup, $novototorccomp<br>";


        $sql1 = "UPDATE bdfrota.tbsupervisor SET valor = $novosaldosup, totorccomp = $novototorccomp WHERE matricula='$matpesq';";
        $resultado = mysql_query($sql1) or die (mysql_error());
        //print "$sql1";

        //tirar da frota
        $sql3="SELECT saldoatual, idtbsaldofrota FROM bdfrota.tbsaldofrota WHERE data_e_hora='$ultdom'; ";
        $resultado3 = mysql_query($sql3) or die (mysql_error());
        $row3 = mysql_fetch_array($resultado3);
            $saldofrotaant = $row3[0];
            $saldofrotaant = str_replace(",",".",$saldofrotaant);

            $idtbsaldofrota = $row3[1];

        $saldofrotanovo = $saldofrotaant - $valor;
        $saldofrotanovo = str_replace(",",".", $saldofrotanovo);

        //print "$saldofrotaant, $idtbsaldofrota, $saldofrotanovo<br>";
        

        $sql4 = "UPDATE bdfrota.tbsaldofrota SET saldoatual=$saldofrotanovo, modificadoem='$hoje' WHERE idtbsaldofrota='$idtbsaldofrota';";
        $resultado4 = mysql_query($sql4) or die (mysql_error());
        //print "$sql4<br>";

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES ('$hoje', '$matpesq', '$mat_autor', '$valor', 4,1, '$valorsuperv');";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        $acao = 'Enviou cota - Fundo frota';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matpesq, $mat_autor, $valor_ant, $valor);

        echo "
        <head>
            
            <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
            <script type='text/javascript'>
                $(document).ready(function() {                
                    
                    alert('Saldo inserido com sucesso!');
                
                    // monta o formulário
                    
                    var formulario = \"<form id='form' hidden method='post' action='../fundofrota.php'>\"
                    +\"<input name='supervisor' value='$matpesq'>\"
                    +\"<input name='cargo' value='superv'>\"                       
                    +\"</form>\";
        
                    // insere o formulário no body
                    $('body').append(formulario);
                    // faz o submit e remove o formulário
                    $('#form').submit().remove();

                });
            </script>
        </head>";

    }elseif($cargof=='coord'){
        $sql="SELECT valor, orcrecebido FROM bdfrota.tbcoord WHERE matricula='$matpesq'; ";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
            $valorcoord = $row[0];
            $orcrecebido = $row[1];
        
        //print"$valorcoord, $orcrecebido, $valor<br>";

        $novosaldocoord = $valorcoord + $valor;
        $novoorcrecebido = $orcrecebido + $valor;

        if(strpos($novosaldocoord, ",") !== false){
            $novosaldocoord = str_replace(",",".",$novosaldocoord);
        }

        if(strpos($novoorcrecebido, ",") !== false){
            $novoorcrecebido = str_replace(",",".",$novoorcrecebido);
        }

        //print "$novosaldocoord, $novoorcrecebido<br>";

        $sql1 = "UPDATE bdfrota.tbcoord SET valor = $novosaldocoord, orcrecebido = $novoorcrecebido WHERE matricula='$matpesq';";
        $resultado = mysql_query($sql1) or die (mysql_error());
        //print "$sql1<br>";

        //tirar da frota
        $sql3="SELECT saldoatual, idtbsaldofrota FROM bdfrota.tbsaldofrota WHERE data_e_hora='$ultdom'; ";
        $resultado3 = mysql_query($sql3) or die (mysql_error());
        $row3 = mysql_fetch_array($resultado3);
            $saldofrotaant = $row3[0];
            if(strpos($saldofrotaant, ",") !== false){
                $saldofrotaant = str_replace(",",".",$saldofrotaant);
            }

            $idtbsaldofrota = $row3[1];
        
        //print "$saldofrotaant, $idtbsaldofrota<br>";

        $saldofrotanovo = $saldofrotaant - $valor;
        if(strpos($saldofrotanovo, ",") !== false){
            $saldofrotanovo = str_replace(",",".",$saldofrotanovo);
        }

        $sql4 = "UPDATE bdfrota.tbsaldofrota SET saldoatual=$saldofrotanovo WHERE idtbsaldofrota='$idtbsaldofrota';";
        $resultado4 = mysql_query($sql4) or die (mysql_error());
        //print "$sql4<br>";

        $sql2="INSERT INTO bdfrota.tbremanejamento(data_e_hora, matricula, matr_autor, valor, perfil_autor, tipo, valor_anterior) VALUES ('$hoje', '$matpesq', '$mat_autor', '$valor', 4,1,'$valorcoord');";
        $resultado2 = mysql_query($sql2) or die (mysql_error());

        $acao = 'Enviou cota - Fundo frota';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matpesq, $mat_autor, $valor_ant, $valor);

        echo "
        <head>
            
            <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
            <script type='text/javascript'>
                $(document).ready(function() {                
                    
                    alert('Saldo inserido com sucesso!');
                
                    // monta o formulário
                    
                    var formulario = \"<form id='form' hidden method='post' action='../fundofrota.php'>\"
                    +\"<input name='coordenador' value='$matpesq'>\"
                    +\"<input name='cargo' value='coord'>\"                       
                    +\"</form>\";
        
                    // insere o formulário no body
                    $('body').append(formulario);
                    // faz o submit e remove o formulário
                    $('#form').submit().remove();

                });
            </script>
        </head>";

       

    }

    

    //echo"<script language='javascript' type='text/javascript'>alert('Saldo inserido com sucesso!');window.location=\"../fundofrota.php\"</script>";
}



?>