<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
$data = date('Y-m-d');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once "../func/log.php";

$aceite = $_POST['aceite'];
$decisao = $_POST['decisao'];

try {
    foreach ($aceite as $id) {
        $sql = "UPDATE bdfrota.tbtoaqui SET aceite = '$decisao', dthoraaceite = '$hoje' WHERE idtbtoaqui = '$id'";
        $resultado = mysql_query($sql);
        if (!$resultado) {
            throw new Exception('Erro ao atualizar registro: ' . mysql_error());
        }
        echo "Registro $id atualizado com decisão $decisao.<br>";

        if ($decisao == '1') {
            $mysqli2 = new mysqli("10.17.0.5", "blvp", "@F3r7n6c2", "bdaniel");
            if ($mysqli2->connect_error) {
                throw new Exception('Erro na conexão com o banco de dados: ' . $mysqli2->connect_error);
            }

            $sql = "SELECT matricula, motivo, obs, latitude, longitude, TIME(hora) as hora, DATE(data) as data, endereco FROM bdfrota.tbtoaqui WHERE idtbtoaqui = '$id'";
            $result = $mysqli2->query($sql);
            if (!$result) {
                throw new Exception('Erro ao buscar detalhes do registro: ' . $mysqli2->error);
            }

            while ($row = $result->fetch_assoc()) {
                $acao = 'aceitou endereco';
                $flag = '1';
                $logresult = enviarlog($hoje, $acao, $flag, $row['matricula'], $matricula, '', '');
                //echo "Log registrado para a matrícula {$row['matricula']}.<br>";

                $matricula = $row['matricula'];
                $numero_de_ordem = 'To Aqui';
                $endereco = $row['endereco'];
                $eta = $row['hora'];
                $fim = $row['hora'];
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];
                $datap = $row['data'];

                if ($endereco == '') {
                    $insert_sql = "INSERT INTO bdfrota.tberroosrota (endereco, provedor, data, eta, fim, numero_de_ordem, flag) 
                                   VALUES('$endereco', '$matricula', '$datap', '$eta', '$fim', '$numero_de_ordem', '0')";
                    $mysqli2->query($insert_sql);
                    //echo "ERRO NO ACEITE para o registro $id.<br>";
                    echo "<script language='javascript' type='text/javascript'>alert('ERRO NO ACEITE');window.location=\"../aceitetoaqui.php\"</script>";
                    exit();
                }

                $segunda_query = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
                $resultado_segunda_query = $mysqli2->query($segunda_query);
                if (!$resultado_segunda_query) {
                    throw new Exception('Erro ao buscar centro de custo: ' . $mysqli2->error);
                }

                if ($resultado_segunda_query) {
                    $row_segunda_query = $resultado_segunda_query->fetch_assoc();
                    $ccusto = $row_segunda_query['ccusto'];
                    //echo "Centro de custo para matrícula $matricula: $ccusto.<br>";

                    if (stripos($ccusto, 'Claro') !== false) {
                        $insert_sql = "INSERT INTO bdfrota.tbosrotalatlong (endereco, data, eta, fim, latitude, longitude, matricula, numero_de_ordem) 
                                       VALUES('$endereco', '$datap', '$eta', '$fim', '$latitude', '$longitude', '$matricula', '$numero_de_ordem')";
                        if (!$mysqli2->query($insert_sql)) {
                            throw new Exception('Erro ao inserir registro em tbosrotalatlong: ' . $mysqli2->error);
                        }
                        //echo "Registro inserido na tabela tbosrotalatlong para matrícula $matricula.<br>";

                        inserirCasa($matricula, $datap, "bdaniel", "bdfrota", "tbosrotalatlong");
                    } else {
                        $insert_sql = "INSERT INTO bdfrota.tbosrota (endereco, data, eta, fim, latitude, longitude, matricula, numero_de_ordem) 
                                       VALUES('$endereco', '$datap', '$eta', '$fim', '$latitude', '$longitude', '$matricula', '$numero_de_ordem')";
                        if (!$mysqli2->query($insert_sql)) {
                            throw new Exception('Erro ao inserir registro em tbosrota: ' . $mysqli2->error);
                        }
                        //echo "Registro inserido na tabela tbosrota para matrícula $matricula.<br>";

                        inserirCasa($matricula, $datap, "bdaniel", "bdfrota", "tbosrota");
                    }
                    $mysqli2->close();
                } else {
                    //echo "Erro ao executar a segunda query para matrícula $matricula.<br>";
                }
            }
        }
    }

    foreach ($aceite as $id) {
        $sql = "SELECT bdfrota.tbtoaqui.matricula, DATE(bdfrota.tbtoaqui.data) as data, time(bdfrota.tbtoaqui.hora) as hora, bdaniel.tbfuncionario.ccusto 
                FROM bdfrota.tbtoaqui 
                INNER JOIN bdaniel.tbfuncionario ON bdfrota.tbtoaqui.matricula = bdaniel.tbfuncionario.matricula 
                WHERE idtbtoaqui = '$id'";
        $resultado = mysql_query($sql);
        if (!$resultado) {
            throw new Exception('Erro ao buscar dados para cálculo de rota: ' . mysql_error());
        }

        while ($row = mysql_fetch_assoc($resultado)) {
            if (stripos($row['ccusto'], 'Claro') !== false) {
                calcularRotaDia($row['matricula'], $row['data'], $row['hora'], 'tbosrotalatlong');
            } else {
                calcularRotaDia($row['matricula'], $row['data'], $row['hora'], 'tbosrota');
            }
            //echo "Rota do dia calculada para matrícula {$row['matricula']} na data {$row['data']}.<br>";
        }
    }

    echo "<script language='javascript' type='text/javascript'>alert('Operação realizada com sucesso!');window.location=\"../aceitetoaqui.php\"</script>";

} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}

function inserirCasa($matricula, $data, $db1, $db2, $table)
{
    try {
        $conn = new mysqli("10.17.0.5", "blvp", "@F3r7n6c2", $db2);
        if ($conn->connect_error) {
            throw new Exception('Erro na conexão com o banco de dados: ' . $conn->connect_error);
        }
        $sqlViagens = "SELECT th.endereco_hotel, dataini, datafim FROM bdviagem.tbsolviagem tsv join bdviagem.tbhoteis th on tsv.idhotel = th.idhotel WHERE matfuncionario = '$matricula' AND '$data' BETWEEN dataini AND datafim and tsv.aceite_do_diretor = 4;";
        $resultViagem = $conn->query($sqlViagens);
        if ($resultViagem->num_rows != 0) {
            $rowViagem = $resultViagem->fetch_assoc();
            $enderecoHotel = $conn->real_escape_string($rowViagem['endereco_hotel']);

            $accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA"; // Substitua pelo seu token do Mapbox

            // URL da API de geocodificação do Mapbox
            $apiUrllxl = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($enderecoHotel) . ".json?access_token=" . $accessToken;

            //echo "$apiUrl <br> <hr> <br>";

            // Fazer a requisição à API de geocodificação
            $response = file_get_contents($apiUrllxl);

            $geocodeData = json_decode($response, true);

            // Verificar se a resposta é válida e se há resultados
            if (!empty($geocodeData['features']) && isset($geocodeData['features'][0]['geometry']['coordinates'])) {
                $latitudeHotel = $geocodeData['features'][0]['geometry']['coordinates'][1];
                $longitudeHotel = $geocodeData['features'][0]['geometry']['coordinates'][0];
            } else {
                $latitudeHotel = null;
                $longitudeHotel = null;
            }
            $sql2 = "SELECT * FROM $db2.$table WHERE matricula = '$matricula' and numero_de_ordem = 'Viagem' and data = '$data'";
            $result2 = $conn->query($sql2);
            if ($result2->num_rows === 0) {
                $dateToday = new DateTime($data, new DateTimeZone('America/Sao_Paulo'));
                $dataIni = new DateTime($rowViagem['dataini'], new DateTimeZone('America/Sao_Paulo'));
                $dataFim = new DateTime($rowViagem['datafim'], new DateTimeZone('America/Sao_Paulo'));
                if ($dateToday == $dataIni) {
                    $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) SELECT CONCAT(endereco, ',', numres, '-', cidade, '-', estado), nome, '$data', '00:00:01', '00:00:01', 'CASA', matricula, latitude, longitude, '0' FROM bdaniel.tbfuncionario where matricula = '$matricula'";
                    $result2 = $conn->query($sql2);
                    echo "$sql2";
                } else {
                    $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) SELECT '$enderecoHotel', nome, '$data', '00:00:01', '00:00:01', 'Viagem', matricula, $latitudeHotel, $longitudeHotel, '0' FROM bdaniel.tbfuncionario where matricula = '$matricula'";
                    $result2 = $conn->query($sql2);
                    echo "$sql2";
                }

                if ($dateToday == $dataFim) {
                    $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) SELECT CONCAT(endereco, ',', numres, '-', cidade, '-', estado), nome, '$data', '23:59:59', '23:59:59', 'CASA', matricula, latitude, longitude, '0' FROM bdaniel.tbfuncionario where matricula = '$matricula'";
                    $result2 = $conn->query($sql2);
                    echo "$sql2";
                } else {
                    $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) SELECT '$enderecoHotel', nome, '$data', '23:59:59', '23:59:59', 'Viagem', matricula, $latitudeHotel, $longitudeHotel, '0' FROM bdaniel.tbfuncionario where matricula = '$matricula'";
                    $result2 = $conn->query($sql2);
                    echo "$sql2";
                }
            }
        } else {
            $sql2 = "SELECT * FROM $db2.$table WHERE matricula = '$matricula' and numero_de_ordem = 'CASA' and data = '$data'";
            $result2 = $conn->query($sql2);
            if (!$result2) {
                throw new Exception('Erro ao verificar registros "CASA": ' . $conn->error);
            }

            if ($result2->num_rows == '0') {
                $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) 
                     SELECT CONCAT(endereco, ',', numres, '-', cidade, '-', estado), nome, '$data', '00:00:01', '00:00:01', 'CASA', matricula, latitude, longitude, '0' 
                     FROM $db1.tbfuncionario WHERE matricula = '$matricula'";
                if (!$conn->query($sql2)) {
                    throw new Exception('Erro ao inserir registro "CASA" no início do dia: ' . $conn->error);
                }
                //echo "Registro 'CASA' inserido no início do dia para matrícula $matricula.<br>";

                $sql2 = "INSERT INTO $db2.$table (endereco, provedor, data, eta, fim, numero_de_ordem, matricula, latitude, longitude, kmrota) 
                     SELECT CONCAT(endereco, ',', numres, '-', cidade, '-', estado), nome, '$data', '23:59:59', '23:59:59', 'CASA', matricula, latitude, longitude, '0' 
                     FROM $db1.tbfuncionario WHERE matricula = '$matricula'";
                if (!$conn->query($sql2)) {
                    throw new Exception('Erro ao inserir registro "CASA" no final do dia: ' . $conn->error);
                }
                //echo "Registro 'CASA' inserido no final do dia para matrícula $matricula.<br>";
            }
            $conn->close();
        }
    } catch (Exception $e) {
        echo 'Erro: ' . $e->getMessage();
    }
}

function calcularRotaDia($matricula, $data, $hora, $table)
{
    try {
        $conn = new mysqli("10.17.0.5", "blvp", "@F3r7n6c2", "bdfrota");
        if ($conn->connect_error) {
            throw new Exception('Erro na conexão com o banco de dados: ' . $conn->connect_error);
        }

        $hoje = $data;
        $dataIniSem = new DateTime($hoje);
        $segunda = $dataIniSem->format('N') == 1 ? $hoje : date('Y-m-d', strtotime('last monday'));

        $sqlDatasMatriculas = "SELECT matricula, '$hoje' as dia FROM $table WHERE data = '$hoje' AND matricula = '$matricula' GROUP BY matricula";
        $resultDatasMatriculas = $conn->query($sqlDatasMatriculas);
        if (!$resultDatasMatriculas) {
            throw new Exception('Erro ao buscar datas e matrículas: ' . $conn->error);
        }

        if ($resultDatasMatriculas->num_rows > 0) {
            while ($row = $resultDatasMatriculas->fetch_assoc()) {
                $matricula = $row['matricula'];
                $dataReferencia = $row['dia'];
                $sqlSaldoSeg = "SELECT * FROM tbsaldo WHERE data = '$segunda' AND matricula = '$matricula'";
                $resultSaldoSeg = $conn->query($sqlSaldoSeg);
                if (!$resultSaldoSeg) {
                    throw new Exception('Erro ao buscar saldo: ' . $conn->error);
                }

                $dataInicio = new DateTime($dataReferencia);
                $distanciasDia = array();
                $dataDia = $dataInicio->format('Y-m-d');

                $sqlCoordenadas = "SELECT REPLACE(latitude, ',', '.') as lat, REPLACE(longitude, ',', '.') as lon, eta 
                                   FROM $table 
                                   WHERE matricula = '$matricula' AND DATE(data) = '$dataDia' AND 
    (CAST(REPLACE(latitude, ',', '.') AS DECIMAL) >= -30.0 AND
    CAST(REPLACE(latitude, ',', '.') AS DECIMAL) <= -15.0 AND
    CAST(REPLACE(longitude, ',', '.') AS DECIMAL) >= -60.0 AND
    CAST(REPLACE(longitude, ',', '.') AS DECIMAL) <= -35.0)
                                   GROUP BY eta ORDER BY eta";
                $resultCoordenadas = $conn->query($sqlCoordenadas);
                if (!$resultCoordenadas) {
                    throw new Exception('Erro ao buscar coordenadas: ' . $conn->error);
                }

                if ($resultCoordenadas->num_rows > 0) {
                    $coordenadas = array();
                    $distanciaDia = 0;
                    while ($rowCoordenadas = $resultCoordenadas->fetch_assoc()) {
                        $coordenadas[] = $rowCoordenadas;
                    }

                    for ($i = 1; $i < count($coordenadas); $i++) {
                        $origemLat = $coordenadas[$i - 1]['lat'];
                        $origemLong = $coordenadas[$i - 1]['lon'];
                        $origemEta = $coordenadas[$i - 1]['eta'];
                        $destinoLat = $coordenadas[$i]['lat'];
                        $destinoLong = $coordenadas[$i]['lon'];
                        $destinoEta = $coordenadas[$i]['eta'];

                        $origem = "$origemLong,$origemLat";
                        $destino = "$destinoLong,$destinoLat";
                        $apiUrl = "https://router.project-osrm.org/route/v1/driving/";
                        $requestUrl = $apiUrl . $origem . ";" . $destino . "?steps=true&alternatives=false";
                        //echo "$requestUrl <br>";

                        // Início do cURL
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $requestUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'User-Agent: MyApp/1.0',
                        ]);

                        $response = curl_exec($ch);

                        // Verificar se ocorreu algum erro
                        if (curl_errno($ch)) {
                            echo 'Erro: ' . curl_error($ch);
                            curl_close($ch);
                            continue; // Salta para o próximo loop
                        }

                        curl_close($ch);

                        $routeData = json_decode($response, true);

                        // Verificar se a rota foi encontrada
                        if ($routeData['code'] === 'Ok') {
                            // Obter a distância da rota (em metros)
                            $distance = $routeData['routes'][0]['distance'];
                            // Converter para quilômetros
                            $distanceKm = $distance / 1000;

                            $updateSQL = "UPDATE $table SET kmrota = $distanceKm WHERE matricula = '$matricula' AND data = '$dataDia' AND eta = '$destinoEta'";
                            //echo "------------- $updateSQL -----------------";

                            // Execute a instrução SQL de UPDATE
                            if ($conn->query($updateSQL) === TRUE) {
                                //echo "Registro atualizado com sucesso para lat=$destinoLat e lon=$destinoLong.";
                            } else {
                                echo "Erro ao atualizar o registro: " . $conn->error;
                            }

                            $distanciaDia += $distanceKm;
                        }
                    }

                    if ($distanciaDia > 0) {
                        $distanciaDia += 5; // Adiciona 5 km, se necessário
                    }

                    $distanciasDia[$dataDia] = $distanciaDia;
                } else {
                    // Se não houver coordenadas para o dia, insere 0 km
                    $distanciasDia[$dataDia] = 0;
                }

                foreach ($distanciasDia as $data => $kmsRodadosDia) {
                    $sqlInsertKmsRodados = "INSERT INTO tbkmrodadosdia (matricula, dia, kmsrodados) VALUES ('$matricula', '$data', $kmsRodadosDia) ON DUPLICATE KEY UPDATE kmsrodados = VALUES(kmsrodados)";
                    if (!$conn->query($sqlInsertKmsRodados)) {
                        throw new Exception('Erro ao inserir km rodados: ' . $conn->error);
                    }
                    //echo "Quilômetros rodados no dia $data: $kmsRodadosDia km para a matrícula $matricula.<br>";

                    $datahoradia = date("Y-m-d H:i:s");
                    $sqlaux = "SELECT * FROM bdfrota.tbtbultimaos WHERE matricula = '$matricula'";
                    $resultaux = $conn->query($sqlaux);
                    if (!$resultaux) {
                        throw new Exception('Erro ao verificar registro de última atualização: ' . $conn->error);
                    }

                    if ($resultaux->num_rows <= 0) {
                        $sqlInsertLog = "INSERT INTO bdfrota.tbtbultimaos (matricula, ultimaatt) VALUES ('$matricula', '$datahoradia')";
                        if (!$conn->query($sqlInsertLog)) {
                            throw new Exception('Erro ao inserir registro de última atualização: ' . $conn->error);
                        }
                        //echo "Registro de última atualização inserido para a matrícula $matricula.<br>";
                    } else {
                        $sqlUpdateLog = "UPDATE bdfrota.tbtbultimaos SET ultimaatt = '$datahoradia' WHERE matricula = '$matricula'";
                        if (!$conn->query($sqlUpdateLog)) {
                            throw new Exception('Erro ao atualizar registro de última atualização: ' . $conn->error);
                        }
                        // echo "Registro de última atualização atualizado para a matrícula $matricula.<br>";
                    }

                    $sqlattOS = "DELETE t1
                                 FROM tbkmrodadosdia t1
                                 INNER JOIN (
                                    SELECT matricula, dia, MIN(idtbkmrodadosdia) AS min_id
                                    FROM tbkmrodadosdia
                                    WHERE dia >= '$segunda' AND dia <= '$hoje' AND matricula = '$matricula'
                                    GROUP BY matricula, dia
                                    HAVING COUNT(*) > 1
                                 ) t2
                                 ON t1.matricula = t2.matricula AND t1.dia = t2.dia AND t1.idtbkmrodadosdia = t2.min_id;";
                    if (!$conn->query($sqlattOS)) {
                        throw new Exception('Erro ao remover registros duplicados: ' . $conn->error);
                    }
                    //echo "Registros duplicados removidos para a matrícula $matricula entre $segunda e $hoje.<br>";

                    $sqlattkmstirados = "UPDATE tbkmrodadosdia AS kr
                                         LEFT JOIN (
                                            SELECT 
                                                matricula,
                                                dataedit,
                                                COALESCE(SUM(CASE WHEN posneg = 1 THEN kmsedit ELSE 0 END), 0) AS kmsedit_sub,
                                                COALESCE(SUM(CASE WHEN posneg = 0 THEN kmsedit ELSE 0 END), 0) AS kmsedit_sum
                                            FROM tbkmdiarioedit
                                            WHERE dataedit >= '$hoje' AND dataedit <= '$hoje' AND matricula = '$matricula'
                                            GROUP BY matricula, dataedit
                                         ) AS ke
                                         ON kr.matricula = ke.matricula AND kr.dia = ke.dataedit
                                         SET kr.kmsrodados = kr.kmsrodados - ke.kmsedit_sub + ke.kmsedit_sum
                                         WHERE kr.dia >= '$hoje' AND kr.dia <= '$hoje'
                                         AND ke.matricula IS NOT NULL;";
                    if (!$conn->query($sqlattkmstirados)) {
                        throw new Exception('Erro ao ajustar quilômetros rodados: ' . $conn->error);
                    }
                    //echo "Quilômetros rodados ajustados para a matrícula $matricula no dia $hoje.<br>";

                    if ($resultSaldoSeg->num_rows > 0) {
                        $sqlattKMOS = "UPDATE tbsaldo AS s
                                       JOIN (
                                            SELECT matricula, ROUND(SUM(kmsrodados),2) AS kms
                                            FROM tbkmrodadosdia
                                            WHERE dia >= '$segunda' AND dia <= '$hoje' AND matricula = '$matricula'
                                            GROUP BY matricula
                                            HAVING SUM(kmsrodados) > 0
                                       ) AS subq
                                       ON s.matricula = subq.matricula
                                       SET s.kmosatual = subq.kms
                                       WHERE s.data = '$segunda'";
                        if (!$conn->query($sqlattKMOS)) {
                            throw new Exception('Erro ao atualizar saldo de quilômetros: ' . $conn->error);
                        }
                        //echo "Saldo de quilômetros atualizados para a matrícula $matricula.<br>";
                    }
                }
            }
        } else {
            // echo "Nenhuma data encontrada na tabela de datas para a matrícula $matricula.<br>";
        }
        $conn->close();

    } catch (Exception $e) {
        echo 'Erro: ' . $e->getMessage();
    }
}
?>