<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <section class="vh-100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    <div class="card shadow-lg mt-5">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4 text-center">Login</h1>
                            <?php 
                                if (isset($_GET['error'])) {
                                    echo "<div class='alert alert-danger'>";                                    
                                    switch ($_GET['error']) {
                                        case 'empty':
                                            echo 'Por favor, preencha todos os campos.';
                                            break;
                                        case 'incorrect_password':
                                            echo 'Senha incorreta!';
                                            break;
                                        case 'user_not_found':
                                            echo 'Usuário não cadastrado! Por favor entre em contanto com o administrador.';
                                            break;
                                    }
                                    echo "</div>";
                                }?>
                            <form method="POST" class="needs-validation" action="login.php">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted fw-semibold" for="usuario">Usuário</label>
                                    <input type="text" class="form-control" name="usuario" id="usuario" autocomplete="username" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted fw-semibold" for="senha">Senha</label>
                                    </div>
                                    <input type="password" class="form-control" name="senha" id="senha" autocomplete="current-password" required>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Entrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-5 text-muted">
                        Desenvolvido por <strong>Oficina Auto</strong> &copy;
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>