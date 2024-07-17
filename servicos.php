<div class="container mt-5">
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <input type="text" name="nome_servico" class="form-control" placeholder="Nome do Serviço" value="<?= isset($_GET['nome_servico']) ? $_GET['nome_servico'] : '' ?>">
            </div>
            <div class="col-md-4 mb-3">
                <input type="date" name="data_inicio" class="form-control" placeholder="Data Início" value="<?= isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '' ?>">
            </div>
            <div class="col-md-4 mb-3">
                <input type="text" name="nome_material" class="form-control" placeholder="Nome do Material" value="<?= isset($_GET['nome_material']) ? $_GET['nome_material'] : '' ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="dashboard.php" class="btn btn-secondary">Limpar Filtros</a>
            </div>
        </div>
    </form>

    <div class="row hidden-md-up mt-5">
        <?php
            include_once "includes/config/banco.php";

            $user = $_SESSION["usuario"];

            $query_servicos = "SELECT
                                s.id_servico,
                                s.nome AS nome_servico,
                                s.descricao AS descricao_servico,
                                s.data_inicio,
                                s.data_fim,
                                s.total,
                                m.id_material,
                                m.nome AS nome_material,
                                m.descricao AS descricao_material,
                                sm.quantidade,
                                sm.preco_unitario,
                                sm.subtotal
                            FROM
                                servico s
                            LEFT JOIN servico_material sm ON s.id_servico = sm.id_servico
                            LEFT JOIN material m ON sm.id_material = m.id_material
                            JOIN usuario u ON s.usuario = u.usuario
                            WHERE
                                u.usuario = ?";

            // Adiciona os filtros na consulta
            if (isset($_GET['nome_servico']) && !empty($_GET['nome_servico'])) {
                $query_servicos .= " AND s.nome LIKE ?";
            }
            if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio'])) {
                $query_servicos .= " AND DATE(s.data_inicio) = ?";
            }
            if (isset($_GET['nome_material']) && !empty($_GET['nome_material'])) {
                $query_servicos .= " AND m.nome LIKE ?";
            }

            $stmt = $banco->prepare($query_servicos);
            $bind_types = 's';
            $bind_values = [$user];

            // Adiciona os parâmetros dos filtros
            if (isset($_GET['nome_servico']) && !empty($_GET['nome_servico'])) {
                $bind_types .= 's';
                $bind_values[] = '%' . $_GET['nome_servico'] . '%';
            }
            if (isset($_GET['data_inicio']) && !empty($_GET['data_inicio'])) {
                $bind_types .= 's';
                $bind_values[] = $_GET['data_inicio'];
            }
            if (isset($_GET['nome_material']) && !empty($_GET['nome_material'])) {
                $bind_types .= 's';
                $bind_values[] = '%' . $_GET['nome_material'] . '%';
            }

            $stmt->bind_param($bind_types, ...$bind_values);
            $stmt->execute();
            $res = $stmt->get_result();

            if($res->num_rows == 0){
                echo "<div class='alert alert-info'>";
                    echo 'Nenhum registro encontrado';
                echo "</div>";
            }
        ?>

    <?php while ($row = $res->fetch_object()): ?>
        <div class='col-12 col-md-4 mb-4'>
            <div class="card card-custom h-100">
                <i id='service-icon' class="fa-solid fa-user-gear text-center p-4"></i>
                <div class="card-body">
                    <div class="title d-flex justify-content-between">
                        <h5 class="card-title "><?=$row->nome_servico?></h5>
                        <?php 
                            $data_ini = new DateTimeImmutable($row->data_inicio);
                            $data_inicio = $data_ini->format('Y-m-d');

                            $data_fim = new DateTimeImmutable($row->data_fim);
                            $data_final = $data_fim->format('Y-m-d');
                        ?>
                        <small><span class='text-end'><?=$data_inicio?> <?=$data_final?></span></small> 
                    </div>
                    
                    <p class="card-text"><?= $row->descricao_servico?></p>
                </div>
                <ul class='card-block'>
                    <?php if ($row->id_material): ?>  
                        <li class='card-text'>Material: <?= $row->nome_material ?></li>
                        <li class='card-text'>Descrição Material: <?= $row->descricao_material ?></li>
                        <li class='card-text'>Quantidade: <?= $row->quantidade ?></li>
                        <li class='card-text'>Preço Unitário: R$<?= $row->preco_unitario ?></li>
                        <li class='card-text'>Subtotal: R$<?= $row->subtotal ?></li>            
                    <?php else: ?>
                        <li class='card-text'>Nenhum material associado a este serviço.</li>
                    <?php endif; ?>
                </ul>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <small class="text-muted">Total do Serviço: <strong>R$<?= number_format($row->total, 2, '.', ",")?></strong></small>
                    <span><a class='btn btn-dark' href="alterar_servico.php?id_servico=<?= $row->id_servico ?>">Editar</a></span>
                </div>
                </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>