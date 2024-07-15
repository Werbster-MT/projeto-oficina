<?php 
    session_start();

    if (empty($_POST) || empty($_POST['usuario']) || empty($_POST['senha'])) {
        echo "<script>location.href='index.php'</script>";
    }

    include('config/banco.php');

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

    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $sql = "Select * from usuario 
            where nome = {$usuario} 
            and senha = {$senha}";

    $res = $banco ->query($sql);

    $row = $res->fetch_object(); 

    if ($res->num_rows) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nivel_acesso'] = $row->nivel_acesso;
        echo "<script>location.href='dashboard.php'</script>";
    }else {
        echo "<div class='alert alert-danger' role='alert'>
                    Usuário e/ou senha incorreto(s)
            </div>"; 
        echo "<script>location.href='index.php'</script>";
    }
?>