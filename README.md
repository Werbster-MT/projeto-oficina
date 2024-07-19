# Sistema de Gestão de Oficina Mecânica

Este é um sistema de gestão de oficina mecânica desenvolvido em PHP com MySQL. Ele permite gerenciar materiais, vendas e serviços executados, com controle de acesso por usuário e rastreamento de alterações nos registros.

## Funcionalidades

- **Cadastro de Usuários**: Possibilidade de cadastrar diferentes tipos de usuários (administrador, vendedor, mecânico, almoxarifado).
- **Autenticação de Usuários**: Login seguro com verificação de senha.
- **Gerenciamento de Materiais**: Adicionar, editar e listar materiais utilizados na oficina.
- **Registro de Vendas**: Adicionar e listar vendas realizadas.
- **Registro de Serviços**: Adicionar e listar serviços prestados.
- **Geração de Relatórios**: Relatórios de vendas e serviços com faturamento total e diário.
- **Controle de Estoque**: Atualização automática do estoque de materiais após a venda ou utilização em serviços.
- **Níveis de Permissão**: Acesso a funcionalidades baseado no tipo de usuário.

## Tecnologias Utilizadas

- **PHP**: Linguagem de programação para o backend.
- **MySQL**: Banco de dados relacional para armazenar as informações.
- **Bootstrap**: Framework CSS para estilização e responsividade.
- **DataTables**: Plugin jQuery para criação de tabelas interativas.

## Requisitos

- **PHP** 7.4 ou superior
- **MySQL** 5.7 ou superior
- Servidor Web (Apache, Nginx, etc.)

## Instalação

1. Clone o repositório para o seu servidor local:
   ```bash
   git clone https://github.com/Werbster-MT/projeto-oficina

2. Importe o banco de dados MySQL:
    * Crie um banco de dados chamado oficina.
    * Importe o arquivo oficina.sql para o seu banco de dados.

3. Configure a conexão com o banco de dados:
No arquivo config/banco.php, ajuste as credenciais de acesso ao banco de dados conforme necessário:
    ```php
    $banco = new mysqli("localhost", "root", "", "oficina");

4. Inicie o servidor web e acesse o sistema pelo navegador:
php -S localhost:8000