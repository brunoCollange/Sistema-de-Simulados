<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Iniciar transação
$conn->begin_transaction();

try {
    // SQL para apagar todas as perguntas de perguntas_mod00
    $sql1 = "DELETE FROM perguntas_mod00";
    $conn->query($sql1);

    // SQL para apagar todas as entradas de nome_mod00
    $sql2 = "DELETE FROM nome_mod00";
    $conn->query($sql2);

    // Se ambas as operações forem bem sucedidas, commit da transação
    $conn->commit();
    echo "<script>alert('Banco de dados deletado com sucesso!');</script>";
} catch (Exception $e) {
    // Se ocorrer erro, rollback da transação
    $conn->rollback();
    echo "<script>alert('Ocorreu algum erro ao apagar o banco de dados: " . $conn->error . "');</script>";
}

// Fechar conexão
$conn->close();
?>

<script>
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 0);
</script>
