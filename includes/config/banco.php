<?php
    // Cria uma nova conexão com o banco de dados MySQL usando mysqli
    $banco = new mysqli("localhost", "root", "", "oficina");

    // Verifica se houve algum erro na conexão
    if ($banco->connect_errno) {
        // Exibe uma mensagem de erro e encerra o script caso ocorra um erro de conexão
        echo "<p>Encontrei um erro $banco->errno --> $banco->error</p>";
        die();
    }

    // Define o conjunto de caracteres para a conexão como UTF-8
    $banco->query("SET NAMES 'utf8'");
    $banco->query("SET character_set_connection=utf8");
    $banco->query("SET character_set_client=utf8");
    $banco->query("SET character_set_results=utf8");
?>