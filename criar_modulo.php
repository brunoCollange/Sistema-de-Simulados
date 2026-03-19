<?php
// Função para encontrar o próximo número de módulo disponível
function encontrarProximoModulo() {
    $ultimoModulo = 0;
    $pastaBase = 'modulo_';
    while (is_dir($pastaBase . str_pad($ultimoModulo, 2, "0", STR_PAD_LEFT))) {
        $ultimoModulo++;
    }
    return str_pad($ultimoModulo, 2, "0", STR_PAD_LEFT);
}

// Criar o próximo módulo
$proximoModulo = encontrarProximoModulo();
$pastaModulo = 'modulo_' . $proximoModulo;

if (!is_dir($pastaModulo)) {
    mkdir($pastaModulo, 0777, true);
    echo "Módulo $proximoModulo criado com sucesso!";
} else {
    echo "Erro: Este módulo já existe.";
}
?>
