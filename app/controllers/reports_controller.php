<?php class ReportsController extends ApplicationController {

  protected $beforeAction = array('authenticated' => 'all');

  public function bestSellingProducts() {
    if(isset($this->params['date']['beginDate']) && $this->params['date']['beginDate'] != null) {
      $this->range = ($this->params['date']['beginDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['beginDate']) . " a " : "Total";
      $this->range .= ($this->params['date']['endDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['endDate']) : "";
      if($this->params['date']['beginDate'] > $this->params['date']['endDate']) {
        Flash::message('danger', 'Período inválido!');
        $this->redirectTo("/relatorios/produtos-mais-vendidos");
      } else {
        $this->reports = Report::bestSellers($this->params['date']);
      }
    } else {
      $this->reports = Report::bestSellers();
      $this->range = "Total";
    }

    $this->action = ViewHelpers::urlFor("/relatorios/produtos-mais-vendidos");
    $this->submit = "Refinar";
    $this->graphics = [['Produto', 'Quantidade Vendida', 'Quantidade Pendente']];

    foreach ($this->reports as $product) {
      $this->graphics[] = [$product->getName(), $product->getTotalSold(), $product->getTotalPending()];
    }
    return json_encode($this->graphics, JSON_NUMERIC_CHECK);
  }

  public function bestEmployee() {

    if(isset($this->params['date']['beginDate']) && $this->params['date']['beginDate'] != null) {
      $this->range = ($this->params['date']['beginDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['beginDate']) . " a " : "Total";
      $this->range .= ($this->params['date']['endDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['endDate']) : "";
      if($this->params['date']['beginDate'] > $this->params['date']['endDate']) {
        Flash::message('danger', 'Período inválido!');
        $this->redirectTo("/relatorios/funcionario-exemplar");
      } else {
        $this->reports = Report::bestEmployee($this->params['date']);
      }
    } else {
      $this->reports = Report::bestEmployee();
      $this->range = "Total";
    }

    $this->action = ViewHelpers::urlFor("/relatorios/funcionario-exemplar");
    $this->submit = "Refinar";
    $this->graphics = [['Funcionário', 'Total Vendido (R$)', 'Total Pendente (R$)']];

    foreach ($this->reports as $user) {
      $this->graphics[] = [$user->getFirstName(), $user->getTotalSold(), $user->getTotalPending()];
    }

    return json_encode($this->graphics, JSON_NUMERIC_CHECK);
  }

  public function leastSoldProducts() {
    if(isset($this->params['date']['beginDate']) && $this->params['date']['beginDate'] != null) {
      $this->range = ($this->params['date']['beginDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['beginDate']) . " a " : "Total";
      $this->range .= ($this->params['date']['endDate']) > 0 ? ViewHelpers::dateFormat($this->params['date']['endDate']) : "";
      if($this->params['date']['beginDate'] > $this->params['date']['endDate']) {
        Flash::message('danger', 'Período inválido!');
        $this->redirectTo("/relatorios/produtos-menos-vendidos");
      } else {
        $this->reports = Report::worstSellers($this->params['date']);
      }
    } else {
      $this->reports = Report::worstSellers();
      $this->range = "Total";
    }

    $this->action = ViewHelpers::urlFor("/relatorios/produtos-menos-vendidos");
    $this->submit = "Refinar";
    $this->graphics = [['Produto', 'Quantidade Vendida', 'Quantidade Pendente']];

    foreach ($this->reports as $product) {
      $this->graphics[] = [$product->getName(), $product->getTotalSold(), $product->getTotalPending()];
    }
    return json_encode($this->graphics, JSON_NUMERIC_CHECK);
  }

}
