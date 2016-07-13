<?php class ApplicationController extends BaseController {
  private $currentUser;

  public function currentUser() {
    if ($this->currentUser === null) {
      $this->currentUser = SessionHelpers::currentUser();
    }
    return $this->currentUser;
  }

  public function authenticated() {
    if (!SessionHelpers::isLoggedIn()) {
      Flash::message('warning', 'Você deve estar logado para acessar esta página');
      $this->redirectTo('/login');
    }
  }

  public function autoCompleteCitiesSearch() {
    $cities = Base::whereCityAsLikeJson($this->params['query']);
    echo $cities;
    exit;
  }
}
