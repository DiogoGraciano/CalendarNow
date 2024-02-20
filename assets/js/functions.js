var nomeModal = "";

function go($url){
  window.location.href = $url
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
  nomeModal = nome;
  $(".modal_container."+nome).show()
}

$(document).ready(function(){		

    var url_base = window.location.href.split("/");
    url_base = url_base[0]+"//"+url_base[2]+"/";
    let url_login = url_base+"login";
    let url_atual = window.location.href;

    qtd_bara = window.location.href.split("/").length

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

    function getInvalid(mensagem){
      return '<div class="invalid-feedback">'+mensagem+'</div>';
    }

    function getValid(mensagem){
      return '<div class="valid-feedback">'+mensagem+'</div>';
    }

    function verificarEDesabilitarBotao() {
      var botao = $('#submit');
      var algumElementoComClasse = $('.is-invalid').length > 0;

      botao.prop('disabled', algumElementoComClasse);
    }
    
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

    $("#closeModel").on("click",function(){$(".modal_container").hide()})

    $("#submitModalConsulta").on("click",function(){

      $('#formConsulta').attr('action', $("#actionConsulta"+nomeModal).val());

      $("#formConsulta").append($(".form-group"))
  
      $("#formConsulta").submit();

    });
    

    function getOptionCidade(option){
      $('#id_cidade').append('<option value="'+option.vl_option+'" '+option.extra_option+'>'+option.nm_option+'</option>')
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
              url_login = url_base+"login";
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
              $(".cpf_cnpj").append(getInvalid("CPF Já cadastrado"));
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
    var cep = parseInt($('#cep').val().replace(/[^0-9]/g,""));

    if(cep > 80000000 && cep < 89999999) { 
      showLoader();
      $.ajax({
        type: "POST",
        url: url_base+"ajax",
        data: {"method":"getEndereco","parameters":cep},
        success: function (response) {
          if (response.sucesso) {
              removeLoader();
              if ($("#id_estado").val() != response.retorno.uf){
                $("#id_estado").val(response.retorno.uf)
                getCidades()
              }
              $("#id_cidade").val(response.retorno.localidade);
              $("#bairro").val(response.retorno.bairro);
              validaBairro()
              $("#rua").val(response.retorno.logradouro);
              validaRua()
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
                $(".email").append(getInvalid("E-mail Já cadastrado"));
              }else{
                $("#email").removeClass('is-invalid')
                $(".invalid-feedback").remove();
              }
          }else{
              removeLoader();
              mensagem("Não possivel verificar E-mail");  
              window.scroll({
                top: 0,
                left: 0,
                behavior: "smooth",
              });                  
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



