<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para obter o nome do módulo mais recente
$modulo_result = $conn->query("SELECT nome_modulo FROM nome_mod00 ORDER BY id DESC LIMIT 1");
if ($modulo_result->num_rows > 0) {
    $nome_modulo = $modulo_result->fetch_assoc()['nome_modulo'];
} else {
    $nome_modulo = 'Módulo 00'; // Nome padrão se não houver nenhum módulo definido
}

// SQL para obter todas as perguntas
$sql = "SELECT * FROM perguntas_mod00";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Ver Perguntas</title>
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

    <header>
        <img class="logo" src="/simulados/images/logoWhite.png">
        <h2 style="color:white; margin-top:5px;">Simulados</h2>
        <a href="/simulados/modulo_00" class="btn btn-primary">Voltar</a>
        <button onclick="window.print()" class="btn btn-secondary">Imprimir Perguntas</button>
    </header>

    <div class="contentverperguntas">
        <div class="verperguntas">

            <?php echo '<h1 style="color:#024c81;">' . $nome_modulo . '</h1>' ?>

            <div class="perguntasbckg">
                <?php
                $contador = 1; // Inicialize o contador

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $respostaCorreta = $row['resposta_correta'];
                        $alternativas = [
                            '<b>A</b>' => $row['opcao1'],
                            '<b>B</b>' => $row['opcao2'],
                            '<b>C</b>' => $row['opcao3'],
                            '<b>D</b>' => $row['opcao4'],
                            '<b>E</b>' => $row['opcao5']
                        ];

                        echo '<div class="pergunta">
                        <p><b><u>Questão ' . $contador . ':</u></b>   
                        <button type="button" class="btn-delete print-hide" style="background:none;border:none;color:red;font-size:14px;" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="setDeleteId(' . $row['id'] . ')">
                            <i class="fa fa-trash"></i>
                        </button></p>
                        <p>' . nl2br($row['pergunta']) . '</p>
                        <ul>';
                        foreach ($alternativas as $letra => $alternativa) {
                            echo '<li>' . $letra . '. ' . nl2br($alternativa) . '</li>';
                        }
                        $letraRespostaCorreta = array_search($respostaCorreta, $alternativas);
                        echo '</ul>
                        <p><b><u>Resposta Correta:</u></b></p>
                        <p style="color:green;">' . $letraRespostaCorreta . '. ' . nl2br($respostaCorreta) . '</p>';

                        $contador++; // Incrementar o contador
                        echo '</div>';
                    }
                } else {
                    echo "<h4 class='text-center'>Nenhuma questão encontrada...</h4>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão de Perguntas -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir esta pergunta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="post" action="delete_pergunta.php">
                        <input type="hidden" name="id" id="questionId" value="">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <!-- Modal exclusão de perguntas -->


    <script>
        function setDeleteId(id) {
            document.getElementById('questionId').value = id;
        }
    </script>

</body>

<footer>
    <p class="text-white">Developed by &copy;Bruno Collange</p>
</footer>

</html>