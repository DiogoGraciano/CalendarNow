function go($url){
  window.location.href = $url
}

function calcularMinutos(tempo) {
  // Dividir o tempo em horas, minutos e segundos
  var partes = tempo.split(":");
  
  // Converter horas, minutos e segundos para números inteiros
  var horas = parseInt(partes[0]);
  var minutos = parseInt(partes[1]);
  var segundos = parseInt(partes[2]);
  
  // Calcular o total de minutos
  var totalMinutos = horas * 60 + minutos + Math.round(segundos / 60);
  
  return totalMinutos;
}

function validaVazio(seletor){
  var valor = $(seletor).val();

  if(valor == '') { 
    $(seletor).removeClass('is-valid').addClass('is-invalid');
    return false; 
  }

  $(seletor).removeClass('is-invalid')//.addClass('is-valid');
}

function validaTime(seletor){
  var valor = $(seletor).val();

  if( valor == ':00:00' ) { 
    $(seletor).removeClass('is-valid').addClass('is-invalid');
    return false; 
  }

  $(seletor).removeClass('is-invalid')//.addClass('is-valid');
}

function validaValor(seletor){
  var valor = parseFloat($(seletor).val());

  if(valor < 0) { 
    $("#valor").removeClass('is-valid').addClass('is-invalid');
    return false; 
  }

  $(seletor).removeClass('is-invalid')//.addClass('is-valid');
}

function openModal(nome){
  $("#close"+nome).on("click",function(){$(".modal_container."+nome).hide()})
  $("#submitModalConsulta").on("click",function(){

    $('#formConsulta').attr('action', $("#actionConsulta"+nome).val());

    $("#formConsulta").append($(".form-group"))

    $("#formConsulta").submit();

  });
  $(".modal_container."+nome).show()
}

function multiplicarTempo(tempo, multiplicador) {
  var partesTempo = tempo.split(":");
  var horas = parseInt(partesTempo[0]);
  var minutos = parseInt(partesTempo[1]);
  var segundos = parseInt(partesTempo[2]);

  var tempoTotalSegundos = horas * 3600 + minutos * 60 + segundos;

  var tempoMultiplicadoSegundos = tempoTotalSegundos * multiplicador;

  var horasMultiplicadas = Math.floor(tempoMultiplicadoSegundos / 3600);
  var minutosMultiplicados = Math.floor((tempoMultiplicadoSegundos % 3600) / 60);
  var segundosMultiplicados = Math.floor(tempoMultiplicadoSegundos % 60);

  var tempoResultado = 
      pad(horasMultiplicadas) + ":" + 
      pad(minutosMultiplicados) + ":" + 
      pad(segundosMultiplicados);

  return tempoResultado;
}

function pad(num) {
  return (num < 10 ? "0" : "") + num;
}

function mensagem(mensagem,type="alert-danger"){
  $('body').prepend('<div class="alert '+type+' mt-1 d-flex justify-content-between align-items-center" role="alert">'+mensagem+'</div>');  
  alertTimeout();
}

function alertTimeout(){
  setTimeout(function(){
    $(".alert").fadeOut("fast", function(){
      $(this).alert('close');
    });	
  }, 4000);
}

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

function getInvalid(mensagem,id){
return '<div id="#'+id+'" class="invalid-feedback">'+mensagem+'</div>';
}

function getValid(mensagem,id){
return '<div id="#'+id+'" class="valid-feedback">'+mensagem+'</div>';
}

function verificarEDesabilitarBotao() {
var botao = $('#submit');
var algumElementoComClasse = $('.is-invalid').length > 0;

botao.prop('disabled', algumElementoComClasse);
}

function getOptionCidade(option){
  $('#id_cidade').append('<option value="'+option.id+'">'+option.nome+'</option>').trigger('change');
}

$(document).ready(function(){		

    var url_base = window.location.href.split("/");
    url_base = url_base[0]+"//"+url_base[2]+"/";
    let url_login = url_base+"login";
    let url_atual = window.location.href;

    qtd_bara = window.location.href.split("/").length
    
    // Chame a função no início e, em seguida, defina um intervalo para verificar periodicamente
    verificarEDesabilitarBotao();
    setInterval(verificarEDesabilitarBotao, 1000);

    setTimeout(function() {
      $(".alert-success").fadeOut("fast", function(){
        $(this).alert('close');
      });				
    }, 6000);			

    setTimeout(function() {
      $(".alert-danger").fadeOut("fast", function(){
        $(this).alert('close');
      });				
    }, 6000);	

    $("#pesquisa").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#tableBody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    $('input[type="time2"]').each(function() {
          $(this).timepicker({
          uiLibrary: 'bootstrap5',
          format: 'HH:MM',
          mode: '24hr', 
          value: $(this).val()
      });
    });
  

    $("select").select2({
      theme: "bootstrap-5",

      language: {
        noResults: function(){
            return "Sem Resultados";
        }
      },
      escapeMarkup: function (markup) {
          return markup;
      }
    });

    $(".qtd_item").on("change",function(){

      var index = $(this).attr('data-index-servico')
      var qtd = parseInt($(this).val());

      if ($("#servico_index_"+index).is(":checked")){
        $('input[data-index-check="'+index+'"]').prop("checked", false);
        var total_atual = parseFloat($("#total").attr('data-vl-total'));
        var total_item = parseFloat($("#total_item_"+index).attr('data-vl-atual'))
        if ($("#servico_index_"+index).is(":checked") && total_atual && total_item){
          var total = total_atual + total_item
          $("#total").attr('data-vl-total',total)
          $("#total").val(total.toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}))
        }
        else if(total_atual > 0 && total_atual && total_item){
          var total = total_atual - total_item
          $("#total").attr('data-vl-total',total) 
          $("#total").val(total.toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}))
        }
      }

      if(qtd>0){
        var valor_base = $("#total_item_"+index).attr('data-vl-base');
        if (valor_base){
          valor_base = parseFloat(valor_base);
          var valor = valor_base*qtd
          $("#total_item_"+index).attr('data-vl-atual',valor);
          $("#total_item_"+index).val(valor.toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}))
        }

        var tempo_base = $("#tempo_item_"+index).attr('data-vl-base');
        if (tempo_base)
          $("#tempo_item_"+index).val(multiplicarTempo(tempo_base, qtd))
      }

    })

    $(".check_item").on("change",function(){
    
      var index = $(this).attr('data-index-check');

      var data1 = new Date($("#dt_ini").val());
      var data2 = new Date($("#dt_fim").val());

      var tempo_outros = 0;

      $("input:checked").each(function(){
        var index_f = $(this).attr('data-index-check');
        if ($(this).attr('data-index-check') != index){
          tempo_outros += calcularMinutos($("#tempo_item_"+index_f).val());
        }
      });

      if(data1 && data2){
        var diferenca = Math.abs(data2 - data1);
        var diferencaEmMinutos = Math.ceil(diferenca / 60000);
      }

      minutos = calcularMinutos($("#tempo_item_"+index).val())

      if (diferencaEmMinutos >= (minutos+tempo_outros)){
        var total_atual = parseFloat($("#total").attr('data-vl-total'));
        var total_item = parseFloat($("#total_item_"+index).attr('data-vl-atual'))
        if (this.checked){
          $("input[data-index-check="+index+"]").prop("checked", true);
          var total = total_atual + total_item
          $("#total").attr('data-vl-total',total)
          $("#total").val(total.toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}))
        }
        else if(total_atual > 0){
          $("input[data-index-check="+index+"]").prop("checked", false);
          var total = total_atual - total_item
          $("#total").attr('data-vl-total',total) 
          $("#total").val(total.toLocaleString("pt-BR", { style: "currency" , currency:"BRL"}))
        }
      }
      else{
        $("input[data-index-check="+index+"]").prop("checked", false);
        mensagem("Quantidade informada passa do tempo maximo de agendamento");
        window.scroll({
          top: 0,
          left: 0,
          behavior: "smooth",
        }); 
      }
    })

    // Remove máscara e limita quantidade de caracteres ao dar focus
    $("#cpf_cnpj").focus(removeMascara);
    function removeMascara() {
        $("#cpf_cnpj").unmask();
        $(this).attr("maxlength","18");
    };

    // Permite somente uso de caracteres numéricos
    $("#cpf_cnpj").keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
    });

    // Não permite colar caracteres
    $('#cpf_cnpj').bind("paste",function(e) {
        e.preventDefault();
    });

    // Identifica e valida se o campo é de CPF ou CNPJ
    $("#cpf_cnpj").blur(CPFouCNPJ);
    function CPFouCNPJ() {
        var cpf_cnpj = $("#cpf_cnpj").val().replace(/[^0-9]/g,"");
        var contador = cpf_cnpj.length;
        if(contador == 0){ 
            //$(".cpf_cnpj").append(getInvalid("Digite o CPF ou CNPJ"))
            $("#cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
        } else if(contador == 11){
            if(validaCPF($("#cpf_cnpj").val())) {
              if (url_login != url_atual)
                existsCpfCnpj()
              else 
                $("#cpf_cnpj").removeClass('is-invalid')
            } else {
                //$(".cpf_cnpj").append(getInvalid("CPF inválido"))
                $("#cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
            }
            $("#cpf_cnpj").mask("999.999.999-99");
        } else if(contador == 14) {
            if(validaCNPJ($("#cpf_cnpj").val())) {
              if (url_login != url_atual)
                existsCpfCnpj()
              else 
                $("#cpf_cnpj").removeClass('is-invalid')
            } else {
                //$(".cpf_cnpj").append(getInvalid("CNPJ inválido"))
                $("#cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
            }
            $("#cpf_cnpj").mask("99.999.999/9999-99");
        } else {
            //$(".cpf_cnpj").append(getInvalid("CPF ou CNPJ inválidos"))
            $("#cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
        }
    }

  // Valida CPF
  function validaCPF(cpf) {  
      cpf = cpf.replace(/[^\d]+/g,'');    
      if(cpf == '') return false;   
      if (
          cpf.length != 11 || 
          cpf == "00000000000" || 
          cpf == "11111111111" || 
          cpf == "22222222222" || 
          cpf == "33333333333" || 
          cpf == "44444444444" || 
          cpf == "55555555555" || 
          cpf == "66666666666" || 
          cpf == "77777777777" || 
          cpf == "88888888888" || 
          cpf == "99999999999" || 
          cpf == "01234567890" )
          return false;      
      add = 0;    
      for (i=0; i < 9; i ++)       
      add += parseInt(cpf.charAt(i)) * (10 - i);  
      rev = 11 - (add % 11);  
      if (rev == 10 || rev == 11)     
          rev = 0;    
      if (rev != parseInt(cpf.charAt(9)))     
          return false;    
      add = 0;    
      for (i = 0; i < 10; i ++)        
          add += parseInt(cpf.charAt(i)) * (11 - i);  
      rev = 11 - (add % 11);  
      if (rev == 10 || rev == 11) 
          rev = 0;    
      if (rev != parseInt(cpf.charAt(10)))
          return false;       
      return true;   
  }

  // Valida CNPJ
  function validaCNPJ(CNPJ) {
      CNPJ = CNPJ.replace(/[^\d]+/g,''); 
      var a = new Array();
      var b = new Number;
      var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
      for (i=0; i<12; i++){
          a[i] = CNPJ.charAt(i);
          b += a[i] * c[i+1];
      }
      if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
      b = 0;
      for (y=0; y<13; y++) {
          b += (a[y] * c[y]);
      }
      if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
      if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
          return false;
      }
      if (CNPJ == 0o0) {
          return false;
      }
      return true;
  }

  function existsCpfCnpj(){
    $.ajax({
      type: "POST",
      url: url_base+"ajax",
      data: {"method":"existsCpfCnpj","parameters":$("#cpf_cnpj").val()},
      success: function (response) {
        if (response.sucesso) {
            if (response.retorno){
              $("#cpf_cnpj").removeClass('is-valid').addClass('is-invalid');
              if(!$("div#\\#cpf_cnpj.invalid-feedback").length)
                $(".cpf_cnpj").append(getInvalid("CPF Já cadastrado","cpf_cnpj"));
            }else{
              $("#cpf_cnpj").removeClass('is-invalid')
              $(".invalid-feedback").remove();
            }
        }else{
            removeLoader();
            mensagem("Não possivel verificar CPF/CNPJ");  
            window.scroll({
              top: 0,
              left: 0,
              behavior: "smooth",
            });                  
        }
      },
    });
  }

  $("#cep").blur(validaCep)
  function validaCep(){
    var cep = $('#cep').val().replace(/[^0-9]/g,"");

    if(cep) { 
      showLoader();
      $.ajax({
        type: "POST",
        url: url_base+"ajax",
        data: {"method":"getEndereco","parameters":cep},
        success: function (response) {
          if (response.sucesso) {
              removeLoader();
              if ($("#id_estado").val() != response.retorno.uf){
                $("#id_estado").val(response.retorno.uf).trigger('change');
                getCidades()
                validaCep()
              }
              $("#id_cidade").val(response.retorno.localidade).trigger('change');
              $("#bairro").val(response.retorno.bairro);
              validaVazio("#bairro");
              $("#rua").val(response.retorno.logradouro);
              validaVazio("#rua");
          }else{
              removeLoader();
              mensagem("Não possivel encontrar CEP");                   
          }
        },
      });
      $('#cep').mask('00000-000');
      $("#cep").removeClass('is-invalid')//.addClass('is-valid');
      return true; 
    }
      
    $("#cep").removeClass('is-valid').addClass('is-invalid');
  }

  $("#id_estado").change(getCidades)
    function getCidades(){
      showLoader();
      $("#id_cidade").empty();
      $.ajax({
        type: "POST",
        url: url_base+"ajax",
        data: {"method":"getCidadeOption","parameters":$("#id_estado").val()},
        success: function (response) {
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

  $("#email").blur(validaEmail)
  function validaEmail(){
    var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
    
    var email = $('#email').val();
    
    if( email == '' || !er.test(email) ) { 
      $("#email").removeClass('is-valid').addClass('is-invalid');
      return false; 
    }

    if (url_login != url_atual){
      $.ajax({
        type: "POST",
        url: url_base+"ajax",
        data: {"method":"existsEmail","parameters":email},
        success: function (response) {
          if (response.sucesso) {
              if (response.retorno){
                $("#email").removeClass('is-valid').addClass('is-invalid');
                if(!$("div#\\#email.invalid-feedback").length)
                  $(".email").append(getInvalid("E-mail Já cadastrado","email"));
              }else{
                $("#email").removeClass('is-invalid')
                $(".invalid-feedback").remove();
              }
          }else{
              removeLoader();
              mensagem("Não possivel verificar E-mail");               
          }
        },
      });
    }

    $("#email").removeClass('is-invalid')//.addClass('is-valid');
  }

  $("#nome").on("blur",function(){validaVazio("#nome")});
  $("#nome_empresa").on("blur",function(){validaVazio("#nome_empresa")});
  $("#nome_agenda").on("blur",function(){validaVazio("#nome_agenda")});
  $("#fantasia").on("blur",function(){validaVazio("#fantasia")});
  $("#razao").on("blur",function(){validaVazio("#razao")});
  $("#senha").on("blur",function(){validaVazio("#senha")});
  $("#bairro").on("blur",function(){validaVazio("#bairro")});
  $("#rua").on("blur",function(){validaVazio("#rua")});
  $("#numero").on("blur",function(){validaVazio("#numero")});

  $("#hora_ini").on("blur",function(){validaTime("#hora_ini")});
  $("#hora_fim").on("blur",function(){validaTime("#hora_fim")});
  $("#hora_almoco_ini").on("blur",function(){validaTime("#hora_almoco_ini")});
  $("#hora_almoco_fim").on("blur",function(){validaTime("#hora_almoco_fim")});

  $("#valor").on("blur",function(){validaValor("#valor")});

  $("#novoCliente").on("click",function(){
    if ($(".col-md-9.col-sm-12.cliente.mb-2.novoCliente").length){
      $(".form-group.cliente").show();
      $(".col-md-9.col-sm-12.cliente.mb-2").removeClass("novoCliente");
      $(".form-group.novoCliente").remove();
      $(this).text("Novo");
    }
    else{
      $(".form-group.cliente").hide();
      $(".col-md-9.col-sm-12.cliente.mb-2").addClass("novoCliente")
      $(".col-md-9.col-sm-12.cliente.mb-2").append('<div class="form-group novoCliente"><label for="cliente">Cliente</label><input type="text" name="cliente" id="cliente" class="form-control" value="" placeholder="Novo Cliente"></div>')
      $(this).text("Escolher");
    }
  })

  $("#telefone").blur(validaTelefone)
  function validaTelefone(){
    //retira todos os caracteres menos os numeros
    telefone = $("#telefone").val();

    telefone = telefone.replace(/\D/g, '');

    if (!(telefone.length >= 10 && telefone.length <= 11)){
      $("#telefone").removeClass('is-valid').addClass('is-invalid');
      return false;
    }

    if (telefone.length == 11 && parseInt(telefone.substring(2, 3)) != 9){
      $("#telefone").removeClass('is-valid').addClass('is-invalid');
      return false;
    }

    for (var n = 0; n < 10; n++) {
        if (telefone == new Array(11).join(n) || telefone == new Array(12).join(n)){
          $("#telefone").removeClass('is-valid').addClass('is-invalid');
          return false;
        }
    }
    
    var codigosDDD = [11, 12, 13, 14, 15, 16, 17, 18, 19,
        21, 22, 24, 27, 28, 31, 32, 33, 34,
        35, 37, 38, 41, 42, 43, 44, 45, 46,
        47, 48, 49, 51, 53, 54, 55, 61, 62,
        64, 63, 65, 66, 67, 68, 69, 71, 73,
        74, 75, 77, 79, 81, 82, 83, 84, 85,
        86, 87, 88, 89, 91, 92, 93, 94, 95,
        96, 97, 98, 99];
    
    if (codigosDDD.indexOf(parseInt(telefone.substring(0, 2))) == -1){ 
      $("#telefone").removeClass('is-valid').addClass('is-invalid');
      return false;
    }

    if (telefone.length > 10)
      $("#telefone").mask('(00) 00000-0000')
    else 
      $("#telefone").mask('(00) 0000-0000')

    $("#telefone").removeClass('is-invalid')//.addClass('is-valid');
    return true;
  }
});



