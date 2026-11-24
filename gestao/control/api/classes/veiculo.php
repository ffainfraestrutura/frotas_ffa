<?php
    date_default_timezone_set('America/Sao_Paulo');
    class Veiculo
    {
        public function showAll()
        {
            $db_host = "10.27.0.100";
            $db_user = "blvp";
            $db_pass = "qrdrps";
            $db_name = "bdfrota";
            $con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $sql = "SELECT * FROM `bdfrota-teste`.tbveiculo;";

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
                return ($resultados);
            }
        }
        public function select($dados_veiculo)
        {
            $db_host = "10.27.0.100";
            $db_user = "blvp";
            $db_pass = "qrdrps";
            $db_name = "bdfrota";
            $con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $sql = "select v.idtbveiculo, v.placa, v.renavam,
                    v.ccusto, v.oficina, v.unidade, m.modelo, ma.marca
                    FROM `bdfrota-teste`.tbveiculo v
                    join `bdfrota-teste`.tbmodeloveic m on m.idtbmodeloveic = v.modelo
                    join `bdfrota-teste`.tbmarcaveic ma on ma.idtbmarcaveic = v.marca
                    where v.ccusto like '%$dados_veiculo%' or v.unidade like '%$dados_veiculo%' 
                    or v.placa like '%$dados_veiculo%' or v.renavam like '%$dados_veiculo%' Limit 10";

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