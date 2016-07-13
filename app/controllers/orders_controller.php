<?php class OrdersController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->product = new Product();
    $this->categories = Category::all();
    $this->action = '/produtos';
    $this->submit = 'Cadastrar';
  }

  public function allowedUser() {
    if (!$this->order->isAllowedUser()){
      Flash::message('warning', 'Este pedido não é seu, curioso!');
      $this->redirectTo('/pedidos');
    }
  }

  public function create(){
    $this->order = new Order();
    $this->order->setClientId($this->params[':client_id']);
    $this->order->setUserId($this->currentUser()->getId());

    if ($this->order->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo("/pedidos/{$this->order->getId()}");
    } else {
      Flash::message('danger', 'Tente novamente.');
      $this->redirectTo('/pedidos');
    }
  }

  public function index() {

    $this->urlSegment = 'pedidos';
    $this->limit = 10;
    $this->page = isset($this->params[':page']) ? $this->params[':page'] : 1;
    $this->offset = ($this->page - 1) * $this->limit;
    $this->totalRegisters = Client::count();
    $this->totalPages = ceil($this->totalRegisters / $this->limit);

    $options = array('limit' => $this->limit, 'offset' => $this->offset);

    $this->orders = Order::all($this->currentUser()->getId(), $options);
  }

  public function close() {
    $this->order = Order::findById($this->params[':id']);

    if ($this->order->closeOrder()) {
      Flash::message('success', "Pedido fechado com sucesso!");
      $this->redirectTo('/pedidos');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/pedidos/' . $this->product->getId() . "/fechar-pedido";
    }
  }

  public function show() {

    $this->order = Order::findById($this->params[':id']);
    $this->allowedUser();
    if(sizeof($this->order->getItems()) == 0) $this->render('empty');

    if($this->order->getStatus() == 0) {
      $this->action = "/pedidos/" . $this->order->getId() . "/atualiza-quantidade-produto/";
      $this->submit['plus'] = '<span class="glyphicon glyphicon-plus" data-toggle="tooltip" title="Aumentar" ></span>';
      $this->submit['minus'] = '<span class="glyphicon glyphicon-minus" data-toggle="tooltip" title="Diminuir" ></span>';
      $this->submit['refresh'] = '<span class="glyphicon glyphicon-refresh" data-toggle="tooltip" title="Atualizar" ></span>';
      $this->close = ViewHelpers::linkTo("/pedidos/{$this->order->getId()}/fechar-pedido", '<span class="glyphicon glyphicon-remove" data-toggle="tooltip" title="Passa Régua!" ></span>', 'class="btn btn-xs btn-primary"' );
    } else {
      $this->action = "/pedidos/" . $this->order->getId();
      $this->close = "";
    }

  }

  public function delete() {
    $this->order = Order::findById($this->params[':id']);

    if ($this->order->delete()) {
      Flash::message('success', "Pedido removido com sucesso! Estoque refeito.");
      $this->redirectTo('/pedidos');
    } else {
      Flash::message('danger', 'Algo deu errado, deixe o pedido aí por garantia ;)');
      $this->redirectTo('/pedidos/' . $this->order->getId());
    }
  }

  public function search() {
    if(!isset($this->params['name'])) $this->redirectTo('/pedidos');
    $this->orders = Order::findByAny($this->params['name']);
  }

  public function addProduct() {
    $this->order = Order::findById($this->params[':id']);
    $this->product = Product::findById($this->params['product_id']);

    if (isset($this->product) && $this->order->addProduct($this->product, $this->params['amount'])) {
      Flash::message('success', "{$this->product->getName()} adicionado com sucesso!");
      $this->redirectTo('/pedidos/' . $this->order->getId());
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->redirectTo('/pedidos/' . $this->order->getId());
    }
  }

  public function removeProduct() {
    $this->order = Order::findById($this->params[':id']);
    $this->item = ItemOrderProduct::findById($this->params[':item_id']);

    if ($this->order->removeProduct($this->item)) {
      Flash::message('success', "{$this->item->getProduct()->getName()} removido com sucesso!");
      $this->redirectTo('/pedidos/' . $this->order->getId());
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->redirectTo('/pedidos/' . $this->order->getId());
    }
  }

  public function update() {
    $this->order = Order::findById($this->params[':id']);
    $this->item = ItemOrderProduct::findById($this->params[':item_id']);

    if($this->params['amount'] < 1 || $this->params['amount'] == NULL) {
      Flash::message('danger', 'Quantidade inválida!');
      $this->redirectTo('/pedidos/' . $this->params[':id']);
    }

    if($this->order->getStatus() == 0) {
      if($this->params['operation'] == 'plus')
        $resp = $this->order->increaseAmount($this->item,$this->params['amount']);
      if($this->params['operation'] == 'minus')
        $resp = $this->order->decreaseAmount($this->item, $this->params['amount']);
      if($this->params['operation'] == 'refresh')
        $resp = $this->order->updateAmount($this->item, $this->params['amount']);
    }

    if ($resp) {
      $this->redirectTo('/pedidos/' . $this->order->getId());
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->redirectTo('/pedidos/' . $this->order->getId());
    }
  }

}


 ?>
