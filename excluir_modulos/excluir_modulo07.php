<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Lista de tabelas a serem excluídas para o módulo 07
$tabelas = array(
    "perguntas_mod07",
    "notas_mod07",
    "nome_mod07"
);

// Exclui cada tabela da lista
foreach ($tabelas as $tabela) {
    $sql = "DROP TABLE IF EXISTS $tabela";
    if ($conn->query($sql) === TRUE) {
    } else {
    }
}

$conn->close();

// Caminho para a pasta do módulo (relativo ao diretório atual)
$module_folder = "modulo_07"; // Nome da pasta do módulo a ser excluída
$parent_directory = __DIR__ . DIRECTORY_SEPARATOR . ".."; // Diretório pai onde está a pasta "modulo_07"

// Caminho completo para a pasta do módulo
$module_path = $parent_directory . DIRECTORY_SEPARATOR . $module_folder;

// Função para apagar todos os arquivos da pasta
function deleteFiles($dir)
{
    // Verifica se é um diretório
    if (is_dir($dir)) {
        // Abre o diretório
        if ($dh = opendir($dir)) {
            // Percorre todos os arquivos e subdiretórios
            while (($file = readdir($dh)) !== false) {
                // Ignora os diretórios . e ..
                if ($file != '.' && $file != '..') {
                    // Verifica se é um diretório
                    if (is_dir($dir . '/' . $file)) {
                        // Chama a função recursivamente para apagar os arquivos deste diretório
                        deleteFiles($dir . '/' . $file);
                    } else {
                        // Se não for um diretório, apaga o arquivo
                        unlink($dir . '/' . $file);
                    }
                }
            }
            // Fecha o diretório
            closedir($dh);
        }
    }
}

// Chama a função para apagar os arquivos da pasta
deleteFiles($module_path);

// Verifica se a pasta do módulo existe
if (is_dir($module_path)) {
    // Tenta remover a pasta do módulo
    if (rmdir($module_path)) {
    } else {
    }
} else {
}
?>
<script>
    setTimeout(function() {
        window.location.href = '/simulados/index.php';
    }, 0);
</script>
