<?php

// Definir as informações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

// Função para conectar ao banco de dados
function conectarBanco($servername, $username, $password, $dbname)
{
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    return $conn;
}

// Cria o banco de dados "simulados" se ele não existir
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql_create_db) === TRUE) {
} else {
    die("Erro ao criar o banco de dados 'simulados': " . $conn->error);
}

$conn->close();

// Conectar ao banco de dados
$conn = conectarBanco($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['criar_modulo'])) {
    // Verificar se já existem 10 módulos
    $pasta_base = 'modulo_';
    $num_modulos = 0;
    $ultimo_modulo = 1;
    while (is_dir($pasta_base . str_pad($ultimo_modulo, 2, "0", STR_PAD_LEFT))) {
        $num_modulos++;
        $ultimo_modulo++;
    }

    if ($num_modulos >= 10) {
        $mensagem = "Erro: O limite máximo de 10 módulos foi atingido.";
    } else {
        // Encontrar o próximo módulo disponível
        $new_module_id = str_pad($ultimo_modulo, 2, '0', STR_PAD_LEFT); // Adiciona zero à esquerda

        // Criar tabela de perguntas para o novo módulo
        $create_questions_table_sql = "CREATE TABLE IF NOT EXISTS perguntas_mod{$new_module_id} (
                                            id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                            pergunta TEXT NOT NULL,
                                            resposta_correta text NOT NULL,
                                            opcao1 text NOT NULL,
                                            opcao2 text NOT NULL,
                                            opcao3 text NOT NULL,
                                            opcao4 text NOT NULL,
                                            opcao5 text NOT NULL
                                        )";

        // Criar tabela de nome para o novo módulo
        $create_name_table_sql = "CREATE TABLE IF NOT EXISTS nome_mod{$new_module_id} (
                                        id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                        nome_modulo VARCHAR(255)
                                    )";

        // Inserir o nome do módulo na tabela de nomes
        $nome_modulo = $_POST['nome_modulo'];
        $insert_name_sql = "INSERT INTO nome_mod{$new_module_id} (nome_modulo) VALUES ('$nome_modulo')";

        // Executar as consultas
        if (
            $conn->query($create_questions_table_sql) === TRUE &&
            $conn->query($create_name_table_sql) === TRUE &&
            $conn->query($insert_name_sql) === TRUE
        ) {
            $mensagem = "<h5>Novo módulo criado com sucesso!</h5>";
            header('Location:index.php');
        } else {
            $mensagem = "<h5>Erro ao criar novo módulo: </h5>" . $conn->error;
        }

        // Criar a pasta do novo módulo
        $pasta_modulo = $pasta_base . $new_module_id;
        if (!is_dir($pasta_modulo)) {
            copyFiles('modulo_00', $pasta_modulo);

            // Modificar o código dos arquivos copiados para direcioná-los às tabelas de dados corretas
            modifyCode($pasta_modulo, $new_module_id);
        }
    }
}

// Função para copiar arquivos de uma pasta para outra
function copyFiles($source, $destination)
{
    // Garante que a pasta de destino exista
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    // Obtém uma lista de todos os arquivos na pasta de origem
    $files = glob($source . '/*');

    // Percorre os arquivos na pasta de origem
    foreach ($files as $file) {
        // Verifica se é um arquivo
        if (is_file($file)) {
            // Obtém o nome do arquivo
            $filename = basename($file);

            // Copia o arquivo para a pasta de destino
            copy($file, $destination . '/' . $filename);
        }
    }
}

// Função para modificar o código dentro dos arquivos
function modifyCode($folder, $new_module_id)
{
    // Obtém uma lista de todos os arquivos na pasta
    $files = glob($folder . '/*');

    // Percorre os arquivos na pasta
    foreach ($files as $file) {
        // Verifica se é um arquivo PHP
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Lê o conteúdo do arquivo
            $content = file_get_contents($file);

            $content = str_replace("00", "$new_module_id", $content);

            // Escreve o conteúdo modificado de volta ao arquivo
            file_put_contents($file, $content);
        }
    }
}

// Fechar a conexão
$conn->close();

?>



<!DOCTYPE html>
<html>

<head>
    <title>Simulados</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap  JS e CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="fontawesome/css/brands.css" rel="stylesheet" />
    <link href="fontawesome/css/solid.css" rel="stylesheet" />
</head>

<body>

    <header>
        <img class="logo" src="images/logoWhite.png">
        <h2 style="color:white; margin-top:20px;">Simulados</h2>
    </header>

    <div class="content text-center bg-light">
        <h2>Bem vindo(a) ao Sistema de Simulados!</h2>
        <?php
        // Conectar ao banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "simulados";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Inicializar o número de módulos existentes como 0
        $num_modulos = 0;

        // Listar os módulos existentes
        $pasta_base = 'modulo_';
        for ($i = 1; $i <= 10; $i++) {
            $module_id = str_pad($i, 2, "0", STR_PAD_LEFT);
            $module_path = $pasta_base . $module_id;
            if (is_dir($module_path)) {
                // Consultar o banco de dados para obter o nome do módulo
                $sql = "SELECT nome_modulo FROM nome_mod{$module_id} LIMIT 1";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nome_modulo = $row['nome_modulo'];
                    echo "<a href='{$module_path}' class='btn btn-primary m-1'>{$nome_modulo}</a>";
                    $num_modulos++; // Incrementar o número de módulos existentes
                }
            }
        }

        if ($num_modulos < 10) {
            echo '<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#myModal"> <i class="fa-solid fa-plus"></i> </button> <br><br>';
        }

        if ($num_modulos <= 0) {
            echo '<h5>Adicione um módulo clicando no + para começar.</h5>';
        } else {
            echo '<h5>Escolha qual módulo deseja acessar.</h5>';
        }
        ?>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Novo Módulo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="criarModuloForm" method='post' action='index.php'>
                            <div class="mb-3">
                                <label for="nomeModulo" class="col-form-label">Nome do Módulo:</label>
                                <input type="text" class="form-control" id="nomeModulo" name="nome_modulo" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="criarModuloBtn" name="criar_modulo">Criar Módulo</button>
                            <div id="loadingSpinner" class="spinner-border text-primary" role="status" style="display: none;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h3>Bons Estudos!</h3>

        <!-- Exibir a mensagem -->
        <?php if (!empty($mensagem)) : ?>
            <div id="mensagem" class="alert alert-<?php echo strpos($mensagem, 'sucesso') !== false ? 'success' : 'danger'; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p class="text-white">Developed by &copy;Bruno Collange</p>
    </footer>


    <script>
        // ocultar a mensagem após 5 segundos
        setTimeout(function() {
            document.getElementById('mensagem').style.display = 'none';
        }, 5000)

        // spinner de carregamento
        document.getElementById('criarModuloForm').addEventListener('submit', function(event) {
            var submitButton = document.getElementById('criarModuloBtn');
            var loadingSpinner = document.getElementById('loadingSpinner');

            submitButton.style.display = 'none'; // Esconde o botão de envio
            loadingSpinner.style.display = 'inline-block'; // Mostra o spinner
        });
    </script>

    <div id="loadingSpinner" class="spinner-border text-primary" role="status" style="display: none;">
        <span class="visually-hidden">Carregando...</span>
    </div>


</body>

</html>