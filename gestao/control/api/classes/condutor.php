<?php
    date_default_timezone_set('America/Sao_Paulo');
    class Condutor
    {
        public function showAll()
        {
            $db_host = "10.27.0.100";
            $db_user = "blvp";
            $db_pass = "qrdrps";
            $db_name = "bdfrota";
            $con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $sql = "SELECT * FROM bdfrota.tbcondutor;";

            $sql = $con->prepare($sql);
            $sql->execute();

            $resultados = array();
            
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $row = array_map('utf8_encode', $row);
                $resultados[] = $row;
            }

            if(!$resultados){
                throw new Exception("Nenhum condutor encontrado");
            }else{
                return $resultados;
            }
        }
        public function select($dados_condutor)
        {
            $db_host = "10.27.0.100";
            $db_user = "blvp";
            $db_pass = "qrdrps";
            $db_name = "bdfrota";
            $con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $sql = "SELECT f.nome as nome, f.matricula as matricula, f.cpf as cpf, cn.numcnh as cnh, f.ccusto as centrocusto FROM bdfrota.tbcondutor c join bdaniel.tbfuncionario f on c.matricula = f.matricula join bdfrota.tbcnh cn on c.matricula = cn.matricula where f.nome like '%$dados_condutor%' or f.matricula like '%$dados_condutor%' or f.ccusto like '%$dados_condutor%' or cn.numcnh like '%$dados_condutor%' group by f.matricula limit 10";

            $sql = $con->prepare($sql);
            $sql->execute();

            $resultados = array();
            
            while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                $row = array_map('utf8_encode', $row);
                $resultados[] = $row;
            }

            if(!$resultados){
                throw new Exception("Nenhum condutor encontrado");
            }else{
                return $resultados;
            }
        }
    }