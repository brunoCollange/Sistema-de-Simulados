<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Query SQL para verificar a existência de registros
$query = "SELECT COUNT(*) AS total FROM perguntas_mod00";
$result = mysqli_query($conn, $query);
// Condição para continuar na página
if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row['total'] > 0) {
    } else {
        header("Location: /simulados/modulo_00");
    }
}

function obterPerguntasAleatorias($conn, $quantidade)
{
    $sql = "SELECT id, pergunta, resposta_correta, opcao1, opcao2, opcao3, opcao4, opcao5 
            FROM perguntas_mod00
            ORDER BY RAND() 
            LIMIT $quantidade";
    $result = $conn->query($sql);
    $perguntas = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pergunta = $row;
            $alternativas = [$row['opcao1'], $row['opcao2'], $row['opcao3'], $row['opcao4'], $row['opcao5']];

            // Embaralhar as alternativas aleatoriamente
            shuffle($alternativas);

            $pergunta['alternativas'] = array_combine(['<b>A</b>', '<b>B</b>', '<b>C</b>', '<b>D</b>', '<b>E</b>'], $alternativas);

            $perguntas[] = $pergunta;
        }
    }

    return $perguntas;
}

$quantidade_perguntas = 10;

$perguntasAleatorias = obterPerguntasAleatorias($conn, $quantidade_perguntas);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Simulado M00</title>
    <link rel="icon" type="image/x-icon" href="/simulados/favicon.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="/simulados/style.css">
    <!-- Bootstrap  JS e CSS -->
    <link href="/simulados/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/simulados/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <!-- Font Awesome -->
    <link href="/simulados/fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="/simulados/fontawesome/css/brands.css" rel="stylesheet" />
    <link href="/simulados/fontawesome/css/solid.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
</head>

<body>

    <header class="text-end">
        <img class="logo" src="/simulados/images/logoWhite.png">
        <a style="margin-top:20px;" class="btn btn-primary" href="index.php">Voltar para a página Inserir Perguntas</a>
    </header>

    <div class="simulado">

        <h1 style="color:#024c81;" class="text-center">Simulado Módulo 00</h1>

        <div class="questoesimulados">
            <form method="post" action="verificar_respostas.php">
                <?php $contador = 1; // Inicialize o contador 
                ?>
                <?php foreach ($perguntasAleatorias as $pergunta) { ?>
                    <h3><?php echo "<p style='font-size:20px;'>" . $contador . '. ' . nl2br($pergunta['pergunta']) . "</p>"; ?></h3>

                    <?php foreach ($pergunta['alternativas'] as $letra => $alternativa) { ?>
                        <label>
                            <input type="radio" name="respostas[<?php echo $pergunta['id']; ?>]" value="<?php echo $alternativa; ?>" required>
                            <?php echo $letra . '. ' . $alternativa; ?>
                        </label><br>
                    <?php } ?>

                    <hr>
                    <?php $contador++; // Incrementar o contador 
                    ?>
                <?php } ?>
                <div class="text-center">
                    <input class="btn btn-success" type="submit" name="submit" value="Responder Tudo e Finalizar">
                </div>
            </form>
        </div>
    </div>

</body>

<footer>
    <p class="text-white">Developed by &copy;Bruno Collange</p>
</footer>

</html>