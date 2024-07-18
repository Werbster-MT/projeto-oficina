<?php
function renderMenu($tipo, $currentPage) {
    $menu = '';

    switch ($tipo) {
        case "vendedor":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'vendas' ? 'active' : '') . "' href='vendas.php'>Vendas</a>
                </li>

                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'adicionar_venda' ? 'active' : '') . "' href='adicionar_venda.php'>Adicionar Venda</a>
                </li>
            ";
            break;
        case "almoxarifado":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'materiais' ? 'active' : '') . "' href='materiais.php'>Materiais</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'adicionar_material' ? 'active' : '') . "' href='adicionar_material.php'>Adicionar Material</a>
                </li>
            ";
            break;
        case "mecanico":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'servicos' ? 'active' : '') . "' href='servicos.php'>Serviços</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'adicionar_servico' ? 'active' : '') . "' href='adicionar_servico.php'>Adicionar Serviço</a>
                </li>
            ";
            break;
        case "admin":
            $menu = "
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'vendas' ? 'active' : '') . "' href='vendas.php'>Vendas</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'servicos' ? 'active' : '') . "' href='servicos.php'>Serviços</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'materiais' ? 'active' : '') . "' href='materiais.php'>Materiais</a>
                </li>
                <li class='nav-item'>
                    <a class='nav-link " . ($currentPage == 'cadastrar_usuario' ? 'active' : '') . "' href='cadastrar_usuario.php'>Cadastrar Usuário</a>
                </li>
            ";
            break;
    }

    return $menu;
}
?>
