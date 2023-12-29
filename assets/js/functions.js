$(document).ready(function(){		

    var url_atual = window.location.href.split("/");
    url_atual = url_atual[0]+"//"+url_atual[2]+"/";

    function showLoader(){
        if (!$("#loader").length){
          $("html").append('<div id="loader"></div>');
          document.querySelector("body").style.display = "none";
        }
        else{
          document.getElementById("loader").style.display = "block";
          document.querySelector("body").style.display = "none";
        }
    }

    function removeLoader(){
      document.getElementById("loader").style.display = "none";
      document.querySelector("body").style.display = "block";
    }

    setTimeout(function() {
      $(".alert").fadeOut("fast", function(){
        $(this).alert('close');
      });				
    }, 4000);			

    $("#pesquisa").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#tableBody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    function getCidades(){
      showLoader();
      $("#id_cidade").empty();
      $.ajax({
        type: "POST",
        url: url_atual+"ajax",
        data: {"method":"getCidadeOption","parameters":$("#id_estado").val()},
        success: function (response) {
          console.log(response);
          if (response.sucesso) {
              removeLoader();
              var retorno = response.retorno;
              retorno.forEach(getOptionCidade);
          }else{
              removeLoader();
              mensagem("Não possivel encontrar cidades");                    
          }
        },
      });
    }

    $("#cep").blur(function(){
      var cep = $("#cep").val().toString();

      if (cep){
        cep = cep.replace(/[^0-9]/g, '');
        showLoader();
        $.ajax({
          type: "POST",
          url: url_atual+"ajax",
          data: {"method":"getEndereco","parameters":cep},
          success: function (response) {
            console.log(response);
            if (response.sucesso) {
                removeLoader();
                if ($("#id_estado").val() != response.retorno.uf){
                  $("#id_estado").val(response.retorno.uf)
                  getCidades()
                }
                $("#id_cidade").val(response.retorno.localidade);
                $("#bairro").val(response.retorno.bairro);
                $("#rua").val(response.retorno.logradouro);
            }else{
                removeLoader();
                mensagem("Não possivel encontrar CEP"); ;                    
            }
          },
        });
      }
    });

    function getOptionCidade($option){
      $('#id_cidade').append('<option value="'+$option.vl_option+'" '+$option.extra_option+'>'+$option.nm_option+'</option>')
    }

    function mensagem($mensagem,type="alert-danger"){
      $('body').prepend('<div class="alert '+type+' mt-1 d-flex justify-content-between align-items-center" role="alert">'+$mensagem+'</div>');
      alertTimeout();
    }

    function alertTimeout(){
      setTimeout(function(){
        $(".alert").fadeOut("fast", function(){
          $(this).alert('close');
        });	
      }, 4000);
    }

    $("#id_estado").change(function() {
      getCidades()
    });

});



