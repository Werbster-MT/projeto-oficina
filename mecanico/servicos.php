<style>
        .card-custom {
            height: 250px; /* Define a altura padrão */
            width: 100%; /* Ocupará a largura total da coluna */
        }

        .card-block {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
</style>

<div class="row hidden-md-up mt-5">
    <?php
        include_once "includes/config/banco.php";

        // Suponha que você tenha o ID do usuário em uma variável
        $user_id = $_SESSION["usuario"]; // Substitua pelo valor real do ID do usuário

        $sql = "SELECT s.nome, s.descricao, s.total 
                FROM servico s 
                JOIN usuario u ON s.usuario = u.usuario 
                WHERE u.usuario = ?";
        $stmt = $banco->prepare($sql);
        $stmt->bind_param('i', $user_id); // 'i' para inteiros, 's' para strings
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows == 0){
            echo "<div class='alert alert-info'>";
                echo 'Nenhum registro encontrado';
            echo "</div>";
        }
        while($row = $res->fetch_object()) {
            echo"<div class='col-12 col-md-3 mb-4'>
                    <div class='card card-custom p-4'>
                        <div class='card-block'>
                            <h4 class='card-title text-center'>$row->nome</h4>
                            <p class='card-text p-y-1'>$row->descricao.</p>
                            <div class='d-flex justify-content-between'>
                                <a href='#' class='card-link'>link</a>
                                <a href='#' class='card-link'>Second link</a>    
                            </div>
                        </div>
                    </div>
                </div>";
        }
    ?>
</div>