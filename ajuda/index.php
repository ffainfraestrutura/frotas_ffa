<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

include "../conecta.php";
header("Content-type: text/html; charset=utf-8");

$nivel_acesso = $_SESSION["perfil"];

// Verificar se há busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$filtro_nivel = isset($_GET['nivel']) ? intval($_GET['nivel']) : '';

// Construir a query base
$sql = "SELECT * FROM bdfrota.tbajuda WHERE nivel_acesso <= " . intval($nivel_acesso);

// Adicionar filtros se existirem
if (!empty($busca)) {
    $busca_escaped = mysqli_real_escape_string($conexao, $busca);
    $sql .= " AND (titulo LIKE '%$busca_escaped%' OR descricao LIKE '%$busca_escaped%')";
}

// if ($filtro_nivel !== '' && is_numeric($filtro_nivel)) {
//     $sql .= " AND nivel_acesso = " . intval($filtro_nivel);
// }

$sql .= " ORDER BY nivel_acesso DESC";

$resultado = mysql_query($sql) or die(mysql_error());

// Converter o resultado para um array
$manuais = [];
if (mysql_num_rows($resultado) > 0) {
    while ($row = mysql_fetch_assoc($resultado)) {
        $manuais[] = $row;
    }
}

// Níveis para o filtro
$niveis = [
    0 => 'Técnico',
    1 => 'Supervisor', 
    2 => 'Coordenador',
    3 => 'Gerente',
    4 => 'Frota'
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist-style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .cards-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px 0;
        }

        .card-manual {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        /* Responsividade com Grid */
        @media (max-width: 1200px) {
            .cards-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .cards-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
        }

        .card-manual:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        /* Estilos para a barra de busca */
        .busca-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-busca {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.15s;
        }

        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .btn-buscar {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.15s;
        }

        .btn-buscar:hover {
            background-color: #0056b3;
        }

        .btn-limpar {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.15s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-limpar:hover {
            background-color: #545b62;
            color: white;
            text-decoration: none;
        }

        .resultado-info {
            margin-top: 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .sem-manuais {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.1rem;
            width: 100%;
            grid-column: 1 / -1;
        }

        .acoes-busca {
            display: flex;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .form-busca {
                flex-direction: column;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .acoes-busca {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2">Manuais de Ajuda ao Sistema de Frota</h1>
                
                <!-- Barra de Busca -->
                <div class="busca-container">
                    <form method="GET" class="form-busca">
                        <div class="form-group">
                            <label for="busca">Buscar por título ou descrição:</label>
                            <input type="text" 
                                   id="busca" 
                                   name="busca" 
                                   class="form-control" 
                                   placeholder="Digite para buscar..."
                                   value="<?php echo htmlspecialchars($busca); ?>">
                        </div>
                        
                        <!-- <div class="form-group">
                            <label for="nivel">Filtrar por nível:</label>
                            <select id="nivel" name="nivel" class="form-control">
                                <option value="">Todos os níveis</option>
                                <?php foreach ($niveis as $valor => $nome): 
                                    if ($valor <= $nivel_acesso): ?>
                                    <option value="<?php echo $valor; ?>" 
                                            <?php echo ($filtro_nivel == $valor) ? 'selected' : ''; ?>>
                                        <?php echo $nome; ?>
                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div> -->
                        
                        <div class="acoes-busca">
                            <button type="submit" class="btn-buscar">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="?" class="btn-limpar">
                                <i class="fas fa-times"></i> Limpar
                            </a>
                        </div>
                    </form>
                    
                    <?php if (!empty($busca) || $filtro_nivel !== ''): ?>
                        <div class="resultado-info">
                            <i class="fas fa-info-circle"></i>
                            <?php 
                                $filtros = [];
                                if (!empty($busca)) {
                                    $filtros[] = "busca: \"$busca\"";
                                }
                                if ($filtro_nivel !== '' && isset($niveis[$filtro_nivel])) {
                                    $filtros[] = "nível: " . $niveis[$filtro_nivel];
                                }
                                echo "Filtrando por: " . implode(" e ", $filtros);
                            ?>
                            • <?php echo count($manuais); ?> manual(is) encontrado(s)
                        </div>
                    <?php endif; ?>
                </div>

                <div id="containertab">
                    <div class="cards-container">
                        <?php if (empty($manuais)): ?>
                            <div class="sem-manuais">
                                <i class="fas fa-book" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                                <p>
                                    <?php if (!empty($busca) || $filtro_nivel !== ''): ?>
                                        Nenhum manual encontrado com os filtros aplicados.
                                    <?php else: ?>
                                        Nenhum manual disponível para o seu nível de acesso.
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($busca) || $filtro_nivel !== ''): ?>
                                    <a href="?" class="btn-limpar" style="margin-top: 10px;">
                                        <i class="fas fa-times"></i> Limpar filtros
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($manuais as $manual): ?>
                                <?php
                                $titulo = htmlspecialchars($manual['titulo'] ?? 'Sem título');
                                $descricao = htmlspecialchars($manual['descricao'] ?? 'Sem descrição');
                                $url_pdf = $manual['url_pdf'];
                                $nivel = $manual['nivel_acesso'] ?? 1;
                                $nivel_texto = $niveis[$nivel] ?? "Nível $nivel";
                                ?>

                                <div class="card-manual">
                                    <div class="card-content" style="
                                                flex-grow: 1;
                                                padding: 20px;
                                            ">
                                        <h3 style="
                                                    margin: 0 0 12px 0;
                                                    font-size: 1rem;
                                                    font-weight: 600;
                                                    color: #333;
                                                ">
                                            <?php echo $titulo; ?>
                                        </h3>

                                        <p style="
                                                margin-bottom: 16px;
                                                color: #666;
                                                font-size: 0.875rem;
                                                line-height: 1.5;
                                                display: -webkit-box;
                                                -webkit-line-clamp: 3;
                                                -webkit-box-orient: vertical;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                            ">
                                            <?php echo $descricao; ?>
                                        </p>

                                        <div style="
                                                    display: flex;
                                                    align-items: center;
                                                    margin-bottom: 16px;
                                                    padding: 4px 8px;
                                                    background-color: #f8f9fa;
                                                    border-radius: 4px;
                                                    font-size: 0.7rem;
                                                    color: #495057;
                                                ">
                                            <i class="fas fa-user-shield" style="margin-right: 6px;"></i>
                                            <?php echo $nivel_texto; ?>
                                        </div>

                                        <?php if (!empty($url_pdf) && $url_pdf != '#'): ?>
                                            <div style="
                                                            display: flex;
                                                            align-items: center;
                                                            margin-top: 12px;
                                                            padding: 8px 12px;
                                                            background-color: #f8f9fa;
                                                            border-radius: 6px;
                                                            border: 1px solid #e9ecef;
                                                        ">
                                                <i class="fas fa-file-pdf" style="
                                                            margin-right: 8px;
                                                            color: #dc3545;
                                                        "></i>
                                                <span style="
                                                                font-size: 0.7rem;
                                                                color: #495057;
                                                            ">
                                                    Documento PDF disponível
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-actions" style="
                                                    padding: 16px 20px;
                                                    border-top: 1px solid #e0e0e0;
                                                    background-color: #f8f9fa;
                                                ">
                                        <?php if (!empty($url_pdf) && $url_pdf != '#'): ?>
                                            <a href="<?php echo $url_pdf; ?>" target="_blank" class="btn-pdf" style="
                                                        display: inline-flex;
                                                        align-items: center;
                                                        justify-content: center;
                                                        width: 100%;
                                                        padding: 8px 16px;
                                                        background-color: #007bff;
                                                        color: white;
                                                        text-decoration: none;
                                                        border-radius: 4px;
                                                        font-size: 0.85rem;
                                                        font-weight: 500;
                                                        transition: background-color 0.2s;
                                                        border: none;
                                                        cursor: pointer;
                                                " onmouseover="this.style.backgroundColor='#0056b3'"
                                                onmouseout="this.style.backgroundColor='#007bff'">
                                                <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i>
                                                Abrir PDF
                                            </a>
                                        <?php else: ?>
                                            <span style="
                                                        display: block;
                                                        width: 100%;
                                                        text-align: center;
                                                        font-size: 0.75rem;
                                                        color: #6c757d;
                                                        font-style: italic;
                                                    ">
                                                Sem documento disponível
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
</body>

</html>