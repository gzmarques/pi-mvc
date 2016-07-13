<?php class ClientsController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->client = new NaturalPerson();
    $this->action = '/clientes/novo';
    $this->submit = 'Cadastrar';
  }

  public function create(){
    if($this->params['client']['type'] == "0")
      $this->client = new NaturalPerson($this->params['client']);
    else
      $this->client = new LegalPerson($this->params['client']);

    if ($this->client->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/clientes');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/clientes/novo';
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function index() {

    $this->urlSegment = 'clientes';
    $this->orderBy = 2;
    $this->limit = 5;
    $this->page = isset($this->params[':page']) ? $this->params[':page'] : 1;
    $this->offset = ($this->page - 1) * $this->limit;
    $this->totalRegisters = Client::count();
    $this->totalPages = ceil($this->totalRegisters / $this->limit);

    $options = array('limit' => $this->limit, 'offset' => $this->offset, 'orderBy' => $this->orderBy);

    $this->clients = Client::all($options);
  }

  public function edit() {
    $this->product = Product::findById($this->params[':id']);
    $this->categories = Category::all();
    $this->action = '/produtos/' . $this->product->getId();
    $this->submit = 'Atualizar';
  }

  public function update(){
    $this->product = Product::findById($this->params[':id']);

    if ($this->product->update($this->params['product'])) {
      Flash::message('success', "Produto atualizado com sucesso!");
      $this->redirectTo('/produtos');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/produtos/' . $this->product->getId();
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function show() {
    $this->product = Product::findById($this->params[':id']);
  }

  public function delete() {
    $this->product = Product::findById($this->params[':id']);
    $this->product->delete();
    $this->redirectTo('/produtos');
  }

  public function search() {
    $this->client = Client::findByName($this->params['name']);
  }

  public function autoCompleteSearch() {
    $clients = Client::whereNameAsLikeJson($this->params['query']);
    echo $clients;
    exit;
  }


}


 ?>
