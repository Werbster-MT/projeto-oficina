<?php 
    require "config/banco.php";

    function cripto($senha) {
        $c = '';
        for ($pos = 0; $pos < strlen($senha); $pos++) {
            $letra = ord($senha[$pos]) + 1; // ord() mostra o cod de uma letra
            // chr() mostra a letra de um código
            $c .= chr($letra);
        }
        return $c;
    }
    
    function gerarHash($senha) {
        $txt = cripto($senha);
        $hash = password_hash($txt, PASSWORD_DEFAULT);
        return $hash;
    }

    function testarHash($senha, $hash) {
        $ok = password_verify(cripto($senha), $hash);
        return $ok;
    }

    function is_logado() {
        if(empty($_SESSION['user'])) {
            return false;
        } else {
            return true;
        }
    }

    function is_user($nome) {
        $t = $_SESSION['tipo'] ?? null;
        if(is_null($t)) {
            return false;
        } else {
            if ($t == $nome) {
                return true;
            }
            else {
                return false;
            }
        }
    }
?>