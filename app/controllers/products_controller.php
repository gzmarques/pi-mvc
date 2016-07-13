<?php class ProductsController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->product = new Product();
    $this->categories = Category::all();
    $this->action = '/produtos';
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $this->product = new Product($this->params['product']);
    $this->categories = Category::all();

    if ($this->product->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/produtos');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/produtos';
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function index() {
    $this->title = 'Produtos';

    $this->urlSegment = 'produtos';
    $this->orderBy = 1;
    $this->limit = 10;
    $this->page = isset($this->params[':page']) ? $this->params[':page'] : 1;
    $this->offset = ($this->page - 1) * $this->limit;
    $this->totalRegisters = Product::count();
    $this->totalPages = ceil($this->totalRegisters / $this->limit);

    $options = array('limit' => $this->limit, 'offset' => $this->offset, 'orderBy' => $this->orderBy);

    $this->products = Product::all($options);
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
    if(!isset($this->params['name'])) $this->redirectTo('/produtos');
    $this->products = Product::findByAny($this->params['name']);
  }

  public function autoCompleteSearch() {
    $products = Product::whereNameAsLikeJson($this->params['query']);
    echo $products;
    exit;
  }

}


 ?>
