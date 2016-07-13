$(document).ready(function() {
  $('*[data-toggle="tooltip"]').tooltip()
  data_confirm();
  toogle_contact_message();
  auto_complete_products_search();
  auto_complete_clients_search();
  auto_complete_city_search();
  clickable();
  calculate_discount();
  charts();
  client_type();
});

/*** Confirm dialog **/
var data_confirm = function () {
   $('a[data-confirm], button[data-confirm]').click( function () {
      var msg = $(this).data('confirm');
      return confirm(msg);
   });
};

/*
 * Show and hide contact message **/
var toogle_contact_message = function () {
   $('#contacts table tbody a.show').on('click', function (event) {
      event.preventDefault();
      var id = $(this).attr('href');
      var msg = $(this).closest('tbody').find(id);
      msg.toggleClass('hidden');
   });
};

/*** Auto Complete */
var auto_complete_products_search = function () {
  $('#autocomplete_products').autocomplete({
    serviceUrl: $('#autocomplete_products').data('url'),
    onSelect: function (suggestion) {
      $('#product_id').val(suggestion.data);
      $('#stock').attr("placeholder", "Em estoque: " + suggestion.stock);
      $('#price').val(suggestion.price);
      $('#stock').prop('disabled', false);
      if(suggestion.stock <= 0)
        $('#stock').prop('disabled', true);
    }
  });
};

var auto_complete_clients_search = function () {
  $('#autocomplete_clients').autocomplete({
    serviceUrl: $('#autocomplete_clients').data('url'),
    onSelect: function (suggestion) {
      $('#client_id').val(suggestion.data);
      console.log(suggestion.value + " " + suggestion.data);
    }
  });
};

var auto_complete_city_search = function () {
  $('#autocomplete_cities').autocomplete({
    serviceUrl: $('#autocomplete_cities').data('url'),
    onSelect: function (suggestion) {
      $('#city_id').val(suggestion.data);
      $('#city_name').val(suggestion.value);
      $('#state_id').val(suggestion.state_id);
      $('#state_name').val(suggestion.state);
      $('#country_id').val(suggestion.country_id);
      $('#country_name').val(suggestion.country);
      console.log(suggestion.value + "/" + suggestion.state_code + " " + suggestion.data);
    }
  });
};

/**Clickable row **/
var clickable = function () {
  $(".clickable_row").click(
    function() {
      window.document.location = $(this).data("href");
    });
}

/** Calculate discount on-the-go **/
var calculate_discount = function () {
  $('#discount').on("change", function () {
        alert("Oi");
        var qty = $('#amount'),
        price = $('#price'),
        discount = $('#discount');

        $('#price').val(amount * price * discount / 100);

      });
};

var charts = function() {

  if($("#chart").length == 0) return null;
  google.charts.load('current', {'packages':['bar']});
  google.charts.setOnLoadCallback(drawStuff);

  function drawStuff() {
    var data_info = $("#chart").data('graphic');
    var data = new google.visualization.arrayToDataTable(data_info);

    var options = {
      title: $('#graphic-title').val(),
      width: 900,
      legend: { position: 'none' },
      chart: { subtitle: $('#graphic-subtitle').val() },
      axes: {
        x: {
          0: { side: 'top', label: $('#top-x').val()} }// Top x-axis.
      },
      bar: { groupWidth: "40%" },
      isStacked: true
    }

    var chart = new google.charts.Bar(document.getElementById('chart'));
    // Convert the Classic options to Material options.
    chart.draw(data, google.charts.Bar.convertOptions(options));
  };
}

var client_type = function() {
  $('#natural_person').click(function(){
    $('#client_type').attr('value', 0);
    $('#label_doc').text('CPF:*');
    $('#doc').prop('placeholder', 'CPF');
    $('#label_name').text('Nome:*').fadeIn();
    $('#name').prop('placeholder', 'Nome');
    $('#label_more_names').text('Sobrenome:*');
    $('#more_names').prop('placeholder', 'Sobrenome');
  })
  $('#legal_person').click(function(){
    $('#client_type').attr('value', 1);
    $('#label_doc').text('CNPJ:*');
    $('#doc').prop('placeholder', 'CNPJ');
    $('#label_name').text('Nome Fantasia:*');
    $('#name').prop('placeholder', 'Nome Fantasia');
    $('#label_more_names').text('Razão Social:*');
    $('#more_names').prop('placeholder', 'Razão Social');
  })

}
