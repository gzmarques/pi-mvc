<?php class CategoriesController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->category = new Category();
    $this->action = '/categorias';
    $this->submit = 'Cadastrar';
  }

  public function create(){
    $this->category = new Category($this->params['category']);

    if ($this->category->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/categorias';
      $this->submit = 'Cadastrar';
      $this->render('new');
    }
  }

  public function index() {
      $this->categories = Category::all();
  }

  public function edit() {
    $this->category = Category::findById($this->params[':id']);
    $this->action = '/categorias/' . $this->category->getId();
    $this->submit = 'Atualizar';
  }

  public function update(){
    $this->category = Category::findById($this->params[':id']);

    if ($this->category->update($this->params['category'])) {
      Flash::message('success', "Categoria atualizada com sucesso!");
      $this->redirectTo('/categorias');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/categorias/' . $this->category->getId();
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }

  public function show() {
    $this->category = Category::findById($this->params[':id']);
    $this->products = Product::findByCategory($this->params[':id']);
  }

  public function delete() {
    $this->category = Category::findById($this->params[':id']);
    $this->category->delete();
    $this->redirectTo('/categorias');
  }

}


 ?>
