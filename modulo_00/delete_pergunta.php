<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM perguntas_mod00 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "Sucesso";
    } else {
        echo "Erro";
    }

    if ($stmt->execute()) {
        // Redirecionamento após a exclusão com sucesso
        header("Location: ver_perguntas.php?status=success");
    } else {
        // Redirecionamento com mensagem de erro
        header("Location: ver_perguntas.php?status=error");
    }

    $stmt->close();
    $conn->close();
}
