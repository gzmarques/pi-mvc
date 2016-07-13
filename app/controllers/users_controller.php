<?php class UsersController  extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function _new() {
    $this->user = new User();
    $this->action = '/registre-se';
    $this->submit = 'Cadastre-se';
  }

  public function create(){
    $this->user = new User($this->params['user']);

    if ($this->user->save()) {
      Flash::message('success', 'Registro realizado com sucesso!');
      $this->redirectTo('/login');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/registre-se';
      $this->submit = 'Cadastre-se';
      $this->render('new');
    }
  }

  public function edit() {
    $this->user = $this->currentUser();
    $this->action = '/perfil';
    $this->submit = 'Atualizar';
  }

  public function update() {
    $this->user = $this->currentUser();

    if ($this->user->update($this->params['user'])) {
      Flash::message('success', 'Registro atualizado com sucesso!');
      $this->redirectTo('/');
    } else {
      Flash::message('danger', 'Existe dados incorretos no seu formulário!');
      $this->action = '/perfil';
      $this->submit = 'Atualizar';
      $this->render('edit');
    }
  }
} ?>
