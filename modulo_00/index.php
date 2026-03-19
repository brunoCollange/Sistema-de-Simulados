<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "simulados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_POST['submit_modulo'])) {
    $nome_modulo = $conn->real_escape_string(trim($_POST['nome_modulo']));

    // Insere o nome do módulo na tabela modulos
    $sql = "INSERT INTO nome_mod00 (nome_modulo) VALUES ('$nome_modulo')";

    if ($conn->query($sql) === true) {
        echo "<script>var showModal = true;</script>";
    } else {
        echo "Erro ao inserir o nome do módulo: " . $conn->error;
    }
}

// Consulta para obter o nome do módulo mais recente
$modulo_result = $conn->query("SELECT nome_modulo FROM nome_mod00 ORDER BY id DESC LIMIT 1");
if ($modulo_result->num_rows > 0) {
    $nome_modulo = $modulo_result->fetch_assoc()['nome_modulo'];
} else {
    $nome_modulo = 'Módulo 00'; // Nome padrão se não houver nenhum módulo definido
}

if (isset($_POST['submit_edited_modulo'])) {
    $edited_nome_modulo = $conn->real_escape_string(trim($_POST['edited_modulo']));
    $sql = "UPDATE nome_mod00 SET nome_modulo = '$edited_nome_modulo' WHERE id = (SELECT id FROM nome_mod00 ORDER BY id DESC LIMIT 1)";

    if ($conn->query($sql) === true) {
        echo "<script>var showModal = true;</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o nome do módulo: " . $conn->error . "');</script>";
    }
}


function customTrim($str)
{
    // Remove espaços em branco no início e no fim, exceto quebras de linha
    return preg_replace('/^\s+|\s+$/u', '', $str);
}

if (isset($_POST['submit'])) {
    $pergunta = $conn->real_escape_string(customTrim($_POST['pergunta']));
    $opcao1 = $conn->real_escape_string(customTrim($_POST['opcao1']));
    $opcao2 = $conn->real_escape_string(customTrim($_POST['opcao2']));
    $opcao3 = $conn->real_escape_string(customTrim($_POST['opcao3']));
    $opcao4 = $conn->real_escape_string(customTrim($_POST['opcao4']));
    $opcao5 = $conn->real_escape_string(customTrim($_POST['opcao5']));
    $resposta_index = $conn->real_escape_string(trim($_POST['resposta_correta']));

    // Obter o texto da resposta correta com base na opção selecionada
    $resposta_correta = ${$resposta_index};

    $sql = "INSERT INTO perguntas_mod00 (pergunta, resposta_correta, opcao1, opcao2, opcao3, opcao4, opcao5) 
    VALUES ('$pergunta', '$resposta_correta', '$opcao1', '$opcao2', '$opcao3', '$opcao4', '$opcao5')";

    if ($conn->query($sql) === true) {
        echo "<script>var showQuestionModal = true;</script>";
    } else {
        echo "<script> alert('Ocorreu algum erro, por favor, insira a pergunta novamente. Se o problema persistir, contate o desenvolvedor do sistema.'); </script>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Inserir Perguntas</title>
    <link rel="icon" type="image/x-icon" href="/simulados/favicon.ico">
    <!-- CSS -->
    <link rel="stylesheet" href="/simulados/style.css">
    <!-- Bootstrap  JS e CSS -->
    <link href="/simulados/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/simulados/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/simulados/fontawesome/css/fontawesome.css" rel="stylesheet" />
    <link href="/simulados/fontawesome/css/brands.css" rel="stylesheet" />
    <link href="/simulados/fontawesome/css/solid.css" rel="stylesheet" />
</head>

<body>

    <header>
        <img class="logo" src="/simulados/images/logoWhite.png">
        <h2 style="color:white; margin-top:5px;">Simulados</h2>
        <a href="/simulados" class="btn btn-primary">Voltar para Home</a>
        <?php
        // Verificar se há perguntas no banco de dados
        $result = $conn->query("SELECT id FROM perguntas_mod00");
        if ($result->num_rows > 2) {
            echo '<a href="simulado.php" class="btn btn-info" >Ir para o simulado</a>';
        }
        ?>
        <a href="ver_perguntas.php" class="btn btn-warning">Ver perguntas inseridas</a>
        <a class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#confirmarExclusao">Excluir Módulo</a>
    </header>

    <div class="inserirperguntas">

        <h2 class="text-center text-success">
            <?php echo $nome_modulo;
            if ($nome_modulo != 'Módulo 00') {
            ?>
                <a href="#editModulo" onclick="showEditForm()" title="Editar Nome do Módulo"><i class="fa-solid fa-pen-to-square" style="font-size:20px; color:black;"></i></a>
            <?php } ?>
        </h2>

        <?php if ($nome_modulo === 'Módulo 00') : ?>
            <div class="modulo-nome text-center">
                <form method="post" action="">
                    <input type="hidden" name="modulo_id" value="1"><!-- ALTERAR CONFORME O MÓDULO -->
                    <input style="width:35%; height:35px;border-radius:5px;padding:2px 5px;" type="text" name="nome_modulo" placeholder="Insira o nome do módulo" required>
                    <input type="submit" name="submit_modulo" value="Salvar" class="btn btn-primary">
                </form>
            </div><br>
        <?php endif; ?>

        <script>
            function showEditForm() {
                var form = document.getElementById('editModuloForm');
                if (form.style.display === 'none') {
                    form.style.display = 'block';
                } else {
                    form.style.display = 'none';
                }
            }
        </script>


        <div id="editModuloForm" style="display:none;" class="text-center">
            <form method="post" action="">
                <input style="width:35%; height:35px;border-radius:5px;padding:2px 5px;" type="text" name="edited_modulo" value="<?php echo $nome_modulo; ?>" required>
                <input type="submit" name="submit_edited_modulo" value="Atualizar Nome do Módulo" class="btn btn-primary">
            </form>
        </div><br>

        <!-- Modal nome_módulo atualizado -->
        <div class="modal fade" id="updateSuccessModal" tabindex="-1" aria-labelledby="updateSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateSuccessModalLabel">Atualização Nome do Módulo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Nome do módulo atualizado com sucesso!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="window.location.href='index.php';">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="text-center" style="color:#024c81;">Insira as Questões:</h1>
        <div class="inserirperguntasbckg">
            <form method="post" action="">
                <p>Questão:</p> <textarea class="textareainserirperguntas" type="text" name="pergunta" required></textarea>
                <p>A.</p> <textarea class="inputinserirperguntas" type="text" name="opcao1" required></textarea>
                <p>B.</p> <textarea class="inputinserirperguntas" type="text" name="opcao2" required></textarea>
                <p>C.</p> <textarea class="inputinserirperguntas" type="text" name="opcao3" required></textarea>
                <p>D.</p> <textarea class="inputinserirperguntas" type="text" name="opcao4" required></textarea>
                <p>E.</p> <textarea class="inputinserirperguntas" type="text" name="opcao5" required></textarea>
                <p>Selecione a resposta correta:</p>
                <select name="resposta_correta" class="inputinserirperguntas" required>
                    <option value="">Escolha a alternativa da resposta correta</option>
                    <option value="opcao1">A</option>
                    <option value="opcao2">B</option>
                    <option value="opcao3">C</option>
                    <option value="opcao4">D</option>
                    <option value="opcao5">E</option>
                </select><br>
                <div class="text-center"><br>
                    <input style="width:60%;" class="btn btn-success" type="submit" name="submit" value="Inserir Pergunta"><br>
                </div>
            </form>
            <br>

            <!-- Modal para Sucesso ao Inserir Pergunta -->
            <div class="modal fade" id="questionSuccessModal" tabindex="-1" aria-labelledby="questionSuccessModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="text-success" class="modal-title" id="questionSuccessModalLabel">Sucesso!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Pergunta inserida com sucesso!
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="window.location.href='index.php';">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmação de Deleção -->
            <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">ATENÇÃO!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Tem certeza que deseja deletar o banco de dados? <br>
                            Esta ação não pode ser desfeita.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Não</button>
                            <button type="button" class="btn btn-success" id="confirmDelete">Sim</button>
                        </div>
                    </div>
                </div>
            </div>


            <?php
            // Verificar se há perguntas no banco de dados
            $result = $conn->query("SELECT id FROM perguntas_mod00");
            if ($result->num_rows > 2) {
                echo '<div class="text-center">                          
                        <a style="width:60%;" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal">Esvaziar Banco de Dados e começar novamente</a>
                      </div>';
            }
            ?>
        </div>
    </div>

    <!-- Modal de confirmação de exclusão do Módulo -->
    <div class="modal fade" id="confirmarExclusao" tabindex="-1" aria-labelledby="confirmarExclusaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarExclusaoLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tem certeza de que realmente deseja excluir este módulo por completo?<br>
                    ATENÇÃO: Este processo é irreversível!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    <a type="button" class="btn btn-success" style="text-decoration:none;color:#fff;" href="/simulados/excluir_modulos/excluir_modulo00.php">Confirmar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        if (typeof showModal !== 'undefined' && showModal) {
            var myModal = new bootstrap.Modal(document.getElementById('updateSuccessModal'), {
                keyboard: false
            });
            myModal.show();
        }
    </script>

    <script>
        if (typeof showQuestionModal !== 'undefined' && showQuestionModal) {
            var myQuestionModal = new bootstrap.Modal(document.getElementById('questionSuccessModal'), {
                keyboard: false
            });
            myQuestionModal.show();
        }
    </script>

    <script>
        document.getElementById('confirmDelete').addEventListener('click', function() {
            window.location.href = 'apagar_banco.php';
        });
    </script>

    <!-- Script para exclusão de módulos -->
    <script>
        document.getElementById('confirmarExclusaoBtn').addEventListener('click', function(event) {
            // Enviar uma requisição POST para o arquivo PHP
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'excluir_modulo.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Se a requisição for bem-sucedida, recarregar a página
                    window.location.reload();
                }
            };
            xhr.send(); // Enviar a requisição
        });
    </script>
</body>

<footer>
    <p class="text-white">Developed by &copy;Bruno Collange</p>
</footer>

</html>