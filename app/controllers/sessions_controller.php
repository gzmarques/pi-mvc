<?php class SessionsController extends ApplicationController {

  public function _new() {
    $this->user = new User();
  }

  public function create() {
    $email = $this->params['user']['email'];
    $password = $this->params['user']['password'];

    $this->user = User::findByEmail($email);
    if ($this->user && $this->user->authenticate($password)) {
      Flash::message('success', 'Usuário autenticado com sucesso!');
      $this->redirectTo('/perfil');
    } else {
      $this->user = new User();
      Flash::message('danger', 'Usuário/senha Inválidos!');
      $this->render('new');
    }
  }

  public function destroy() {
    SessionHelpers::logOut();
    Flash::message('success', 'Logout com sucesso!');
    $this->redirectTo('/');
  }
} ?>
