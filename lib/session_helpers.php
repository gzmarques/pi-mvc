<?php
class SessionHelpers {

  public static function shouldBeAutenticated(){
    if (!(isset($_SESSION['user']))) {
      Flash::message('danger', 'Você deve estar logado para acessar está página!');
      ViewHelpers::redirect_to('/pages/sessions/new');
    }
  }

  public static function shouldNotBeAutenticated(){
    if (isset($_SESSION['user'])) {
      Flash::message('warning', 'Você deve estar deslogado para acessar está página!');
      ViewHelpers::redirectTo('/');
    }
  }

  public static function currentUser() {
    if (isset($_SESSION['user']['id']))
      return User::findById($_SESSION['user']['id']);

    return null;
  }

  public static function logIn($user) {
    $_SESSION['user']['id'] = $user->getId();
  }

  public static function isLoggedIn() {
    return isset($_SESSION['user']);
  }

  public static function logOut() {
    unset($_SESSION['user']);
  }
} ?>
