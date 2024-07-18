<?php
function renderMenu($tipo, $currentPage) {
    $menu = '';

    switch ($tipo) {
        case "vendedor":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'vendas' ? 'active' : '') . "' href='dashboard.php?page=vendas'>Vendas</a>
                </li>
            ";
            break;
        case "almoxarifado":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'materiais' ? 'active' : '') . "' href='dashboard.php?page=materiais'>Materiais</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'adicionar_material' ? 'active' : '') . "' href='dashboard.php?page=adicionar_material'>Adicionar Material</a>
                </li>
            ";
            break;
        case "mecanico":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'servicos' ? 'active' : '') . "' href='dashboard.php?page=servicos'>Serviços</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'adicionar_servico' ? 'active' : '') . "' href='dashboard.php?page=adicionar_servico'>Adicionar Serviço</a>
                </li>
            ";
            break;
        case "admin":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'vendas' ? 'active' : '') . "' href='dashboard.php?page=vendas'>Vendas</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'servicos' ? 'active' : '') . "' href='dashboard.php?page=servicos'>Serviços</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'materiais' ? 'active' : '') . "' href='dashboard.php?page=materiais'>Materiais</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'cadastrar_usuario' ? 'active' : '') . "' href='dashboard.php?page=cadastrar_usuario'>Cadastrar Usuário</a>
                </li>
            ";
            break;
    }

    return $menu;
}
?>
