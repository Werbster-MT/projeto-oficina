<?php 
    // Inclui o arquivo de configuração do banco de dados
    require "config/banco.php";

    // Função para criptografar a senha
    function cripto($senha) {
        $c = '';
        // Loop através de cada caractere da senha
        for ($pos = 0; $pos < strlen($senha); $pos++) {
            // ord() retorna o código ASCII do caractere
            $letra = ord($senha[$pos]) + 1; // Adiciona 1 ao código ASCII
            // chr() converte o código ASCII de volta para um caractere
            $c .= chr($letra); // Adiciona o caractere criptografado à string resultante
        }
        return $c; // Retorna a senha criptografada
    }
    
    // Função para gerar o hash da senha
    function gerarHash($senha) {
        // Criptografa a senha
        $txt = cripto($senha);
        // Gera um hash da senha criptografada usando o algoritmo bcrypt
        $hash = password_hash($txt, PASSWORD_DEFAULT);
        return $hash; // Retorna o hash gerado
    }

    // Função para verificar a senha contra o hash armazenado
    function testarHash($senha, $hash) {
        // Verifica se a senha criptografada corresponde ao hash armazenado
        $ok = password_verify(cripto($senha), $hash);
        return $ok; // Retorna true se corresponder, false caso contrário
    }
?>