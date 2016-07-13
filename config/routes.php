<?php
    require 'application.php';
    $router = new Router($_SERVER['REQUEST_URI']);

    $router->get('/', array('controller' => 'HomeController', 'action' => 'index'));

    /* Rotas para os contatos
    -------------------------
    $router->get('/fale-conosco', array('controller' => 'ContactsController', 'action' => 'index'));
    $router->post('/fale-conosco', array('controller' => 'ContactsController', 'action' => 'send'));
    $router->get('/mensagens', array('controller' => 'ContactsController', 'action' => 'show'));
    /* Fim das rotas para os contatos
    --------------------------------- */

    /* Rotas para os usuários
    -------------------------
    $router->get('/registre-se', array('controller' => 'UsersController', 'action' => '_new'));
    $router->post('/registre-se', array('controller' => 'UsersController', 'action' => 'create'));*/
    $router->get('/perfil', array('controller' => 'UsersController', 'action' => 'edit'));
    $router->post('/perfil', array('controller' => 'UsersController', 'action' => 'update'));
    /* Fim das rotas para os usuários
    --------------------------------- */

    /* Rotas para os sessões
    ------------------------- */
    $router->get('/login', array('controller' => 'SessionsController', 'action' => '_new'));
    $router->post('/login', array('controller' => 'SessionsController', 'action' => 'create'));
    $router->get('/logout', array('controller' => 'SessionsController', 'action' => 'destroy'));
    /* Fim das rotas para os sessões
      --------------------------------- */

    /* Rotas para as categorias
    ------------------------- */
    $router->get('/categorias/nova', array('controller' => 'CategoriesController', 'action' => '_new'));
    $router->post('/categorias', array('controller' => 'CategoriesController', 'action' => 'create'));
    $router->get('/categorias', array('controller' => 'CategoriesController', 'action' => 'index'));
    $router->get('/categorias/:id/editar', array('controller' => 'CategoriesController', 'action' => 'edit'));
    $router->post('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'update'));
    $router->get('/categorias/:id', array('controller' => 'CategoriesController', 'action' => 'show'));
    $router->get('/categorias/:id/deletar', array('controller' => 'CategoriesController', 'action' => 'delete'));
    /* Fim das rotas para as categorias
    --------------------------------- */

    /* Rotas para os produtos
    ------------------------- */
    $router->get('/produtos/auto_complete_search', array('controller' => 'ProductsController', 'action' => 'autoCompleteSearch'));
    $router->get('/produtos/novo', array('controller' => 'ProductsController', 'action' => '_new'));
    $router->post('/produtos/busca', array('controller' => 'ProductsController', 'action' => 'search'));
    $router->post('/produtos', array('controller' => 'ProductsController', 'action' => 'create'));
    $router->get('/produtos', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/pagina/:page', array('controller' => 'ProductsController', 'action' => 'index'));
    $router->get('/produtos/:id/editar', array('controller' => 'ProductsController', 'action' => 'edit'));
    $router->post('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'update'));
    $router->get('/produtos/:id', array('controller' => 'ProductsController', 'action' => 'show'));
    $router->get('/produtos/:id/deletar', array('controller' => 'ProductsController', 'action' => 'delete'));
    /* Fim das rotas para os produtos
    --------------------------------- */

    /* Rotas para os clientes
    -------------------------*/
    $router->get('/clientes/pagina/:page', array('controller' => 'ClientsController', 'action' => 'index'));
    $router->get('/clientes/auto_complete_search', array('controller' => 'ClientsController', 'action' => 'autoCompleteSearch'));
    $router->get('/clientes/novo', array('controller' => 'ClientsController', 'action' => '_new'));
    $router->post('/clientes/novo', array('controller' => 'ClientsController', 'action' => 'create'));
    $router->get('/clientes/busca/', array('controller' => 'ClientsController', 'action' => 'search'));
    $router->get('/clientes', array('controller' => 'ClientsController', 'action' => 'index'));
    $router->post('/clientes', array('controller' => 'ClientsController', 'action' => 'update'));
    /* Fim das rotas para os clientes
    --------------------------------- */

    /* Rotas para os pedidos
    ------------------------- */
    $router->post('/pedidos/:id/atualiza-quantidade-produto/:item_id', array('controller' => 'OrdersController', 'action' => 'update'));
    $router->post('/pedidos/:id/adiciona-produto/', array('controller' => 'OrdersController', 'action' => 'addProduct'));
    $router->get('/pedidos/pagina/:page', array('controller' => 'OrdersController', 'action' => 'index'));
    $router->get('/clientes/:client_id/pedidos/novo', array('controller' => 'OrdersController', 'action' => 'create'));
    $router->post('/pedidos/novo', array('controller' => 'OrdersController', 'action' => 'create'));
    $router->get('/pedidos/:id/fechar-pedido', array('controller' => 'OrdersController', 'action' => 'close'));
    $router->post('/pedidos/busca/', array('controller' => 'OrdersController', 'action' => 'search'));
    $router->get('/pedidos', array('controller' => 'OrdersController', 'action' => 'index'));
    $router->get('/pedidos/:id', array('controller' => 'OrdersController', 'action' => 'show'));
    $router->get('/pedidos/:id/deletar', array('controller' => 'OrdersController', 'action' => 'delete'));
    $router->get('/pedidos/:id/remove-produto/:item_id', array('controller' => 'OrdersController', 'action' => 'removeProduct'));
    /* Fim das rotas para os pedidos
    --------------------------------- */

    /* Rotas para os relatórios
    -------------------------*/
    $router->get('/relatorios/produtos-mais-vendidos', array('controller' => 'ReportsController', 'action' => 'bestSellingProducts'));
    $router->post('/relatorios/produtos-mais-vendidos', array('controller' => 'ReportsController', 'action' => 'bestSellingProducts'));
    $router->get('/relatorios/funcionario-exemplar', array('controller' => 'ReportsController', 'action' => 'bestEmployee'));
    $router->post('/relatorios/funcionario-exemplar', array('controller' => 'ReportsController', 'action' => 'bestEmployee'));
    $router->get('/relatorios/produtos-menos-vendidos', array('controller' => 'ReportsController', 'action' => 'leastSoldProducts'));
    $router->post('/relatorios/produtos-menos-vendidos', array('controller' => 'ReportsController', 'action' => 'leastSoldProducts'));
    /* Fim das rotas para os relatórios
    --------------------------------- */

    /* Rotas para as cidades
    -------------------------*/
    $router->get('/cidades/auto_complete_cities_search', array('controller' => 'ApplicationController', 'action' => 'autoCompleteCitiesSearch'));
    /* Fim das rotas para as cidades
    --------------------------------- */


    $router->load();
?>
