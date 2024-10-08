<?php
namespace app\helpers;

/**
 * Classe de funções utilitárias
 */
class functions{
    
    /**
     * Retorna o diretório raiz do servidor
     *
     * @return string O diretório raiz do servidor
     */
    public static function getRaiz():string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    /**
     * Obtém a URI PATH atual da requisição.
     *
     * @return string   Retorna a URI atual da requisição.
     */
    public static function getUriPath():string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Obtém a URI QUERY atual da requisição.
     *
     * @return string   Retorna a URI atual da requisição.
     */
    public static function getUriQuery():string
    {
        return parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY)?:"";
    }

     /**
     * Obtém a array URI QUERY atual da requisição.
     *
     * @return array Retorna a URI atual da requisição.
     */
    public static function getUriQueryArray():array
    {
        $result = [];
        $query = functions::getUriQuery();

        !$query?:parse_str($query,$result);

        return $result ? $result : [];
    }
    
    /**
     * Retorna a URL base do site
     *
     * @return string A URL base do site
     */
    public static function getUrlBase():string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";
    }

    public static function getUrlCompleta(){
        return rtrim(self::getUrlBase(),"/").$_SERVER['REQUEST_URI'];
    }
    
    /**
     * Decodifica uma string UTF-8 codificada para URL
     *
     * @param string $str A string codificada para URL
     * @return string A string decodificada
     */
    public static function utf8_urldecode(string $str):string
    {
        return mb_convert_encoding(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)),'UTF-8');
    }
    
    /**
     * Remove todos os caracteres não numéricos de uma string
     *
     * @param string $value A string a ser filtrada
     * @return string A string contendo apenas números
     */
    public static function onlynumber(string $value):string
    {
        $value = preg_replace("/[^0-9]/","", $value);
        return $value;
    }

    /**
     * Converte uma string para o formato de data e hora do banco de dados
     *
     * @param string $string A string contendo a data e hora
     * @return string|bool A string formatada ou false se falhar
     */
    public static function dateTimeBd(string $string):string|bool
    {
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('Y-m-d H:i:s');

        return false;
    }

    /**
     * Converte uma string para o formato de data e hora BR
     *
     * @param string $string A string contendo a data e hora
     * @return string|bool A string formatada ou false se falhar
     */
    public static function dateTimeBr(string $string):string|bool
    {
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('d/m/Y H:i:s');

        return false;
    }

    /**
     * Validada se uma cor é valida
     *
     * @param string $string A string contendo a data
     * @return string|bool A string formatada ou false se falhar
     */
    public static function validaCor(string $cor):string|bool
    {
        // Expressão regular para verificar cor hexadecimal
        $padrao_hex = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
        
        // Expressão regular para verificar cor RGB
        $padrao_rgb = '/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/';
        
        // Verificar se a cor é hexadecimal ou RGB
        if (preg_match($padrao_hex, $cor) || preg_match($padrao_rgb, $cor)) {
            return $cor;
        } else {
            return false;
        }
    }

    /**
     * Converte uma string para o formato de data do banco de dados
     *
     * @param string $string A string contendo a data
     * @return string|bool A string formatada ou false se falhar
     */
    public static function dateBd(string $string):string|bool
    {
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('Y-m-d');

        return false;
    }

     /**
     * Converte uma string para o formato de data BR
     *
     * @param string $string A string contendo a data
     * @return string|bool A string formatada ou false se falhar
     */
    public static function dateBr(string $string):string|bool
    {
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('d/m/Y');

        return false;
    }

    public static function validaCpfCnpj($cpf_cnpj):bool
    {
        $cpf_cnpj = preg_replace('/[^0-9]/', '', (string)$cpf_cnpj);

        if (strlen($cpf_cnpj) == 14)
            return self::validaCnpj($cpf_cnpj);
        elseif(strlen($cpf_cnpj) == 11)
            return self::validaCpf($cpf_cnpj);
        else 
            return false;
    }

    /**
     * Valida se o cnpj é valido
     *
     * @param string $cnpj A string contendo a data
     * @return bool Se for validado true senão fals
    */
    public static function validaCnpj($cnpj):bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);

        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    /**
     * Valida se o cpf é valido
     *
     * @param string $cnpj A string contendo a data
     * @return bool Se for validado true senão false
    */
    public static function validaCpf($cpf):bool
    {
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/', '', (string)$cpf);
        
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /**
     * Formata um CNPJ ou CPF
     *
     * @param string $value O valor do CNPJ ou CPF
     * @return string O valor formatado
     */
    public static function formatCnpjCpf(?string $value):string|bool
    {
        if(!$value){
            return false;
        }

        $CPF_LENGTH = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return functions::mask($cnpj_cpf, '###.###.###-##');
        } 
        
        return functions::mask($cnpj_cpf, '##.###.###/####-##');
    }

    /**
     * Aplica uma máscara a uma string
     *
     * @param string $val A string original
     * @param string $mask A máscara a ser aplicada
     * @return string A string com a máscara aplicada
     */
    public static function mask(string $val,string $mask):string
    {
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if($mask[$i] == '#') {
                if(isset($val[$k])) $maskared .= $val[$k++];
            } else {
                if(isset($mask[$i])) $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }


    /**
     * Valida se uma email é valido
     *
     * @param string $email A string com o email
     * @return bool se é valido
    */
    public static function validaEmail($email):bool
    {
        // Usar filter_var com FILTER_VALIDATE_EMAIL para validar o e-mail
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida se os dias são validos
     *
     * @param string $dias A string com os dias
     * @return bool se é valido
    */
    public static function validarDiasSemana($dias_semana):bool
    {
        
        // Dividir a lista em dias individuais
        $dias = explode(",", $dias_semana);
        
        if(count($dias) <= 7){
            // Verificar cada dia individualmente
            foreach ($dias as $dia) {
                $dia = trim($dia);
                if (!in_array($dia, ["","dom", "seg", "ter", "qua", "qui", "sex", "sab"])) {
                    return false; // Dia inválido encontrado
                }
            }
            
            return true; // Todos os dias são válidos
        }

        return false;
    }

    /**
     * Valida se uma horario é valido
     *
     * @param string $horario A string com o horario
     * @return bool se é valido
    */
    public static function validaHorario(string $horario):bool
    {
        // Expressão regular para validar o formato HH:MM:SS
        $padrao_horario = '/^([01]?[0-9]|2[0-3]):([0-5]?[0-9]):([0-5]?[0-9])$/';
        
        // Verificar se o horário corresponde ao padrão
        if (preg_match($padrao_horario, $horario, $matches)) {
            // Verificar se os valores de hora, minuto e segundo estão dentro dos limites corretos
            $hora = intval($matches[1]);
            $minuto = intval($matches[2]);
            $segundo = intval($matches[3]);
            
            if ($hora >= 0 && $hora <= 23 && $minuto >= 0 && $minuto <= 59 && $segundo >= 0 && $segundo <= 59) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Formata uma string de tempo para o formato HH:MM:SS ou HH:MM
     *
     * @param string $time A string de tempo a ser formatada
     * @return string A string de tempo formatada
     */
    public static function formatTime(string $time):string
    {
        if ($tamanho = substr_count($time,":")){
            if ($tamanho == 2){
                return $time;
            }
            if ($tamanho == 1){
                return $time.":00";
            }
        }
        else{
            return $time.":00:00";
        }
    }

    /**
     * Multiplica um tempo pela quantidade informada
     *
     * @param string $tempo A string de tempo a ser modificada
     * @return int A string de tempo sem os segundos
    */
    public static function multiplicarTempo(string $tempo,int $quantidade):string
    {
        // Divide o tempo em horas, minutos e segundos
        list($horas, $minutos, $segundos) = explode(':', $tempo);
    
        // Converte tudo para segundos
        $totalSegundos = $horas * 3600 + $minutos * 60 + $segundos;
    
        // Multiplica pelos segundos pela quantidade especificada
        $totalSegundos *= $quantidade;
    
        // Calcula as novas horas, minutos e segundos
        $novasHoras = floor($totalSegundos / 3600);
        $totalSegundos %= 3600;
        $novosMinutos = floor($totalSegundos / 60);
        $novosSegundos = $totalSegundos % 60;
    
        // Formata a nova hora no formato HH:MM:SS
        return sprintf('%02d:%02d:%02d', $novasHoras, $novosMinutos, $novosSegundos);
    }

    /**
     * Remove os segundos de uma string de tempo
     *
     * @param string $time A string de tempo a ser modificada
     * @return string A string de tempo sem os segundos
     */
    public static function removeSecondsTime($time):string
    {
        if ($tamanho = substr_count($time,":")){
            if ($tamanho == 2){
                $time = explode(":",$time);
                return $time[0].":".$time[1];
            }
            if ($tamanho == 1){
                return $time;
            }
        }
        else{
            return $time.":00";
        }
    }

    /**
     * Formata uma string contendo dias, substituindo vírgulas por espaços
     *
     * @param string $dias A string contendo os dias
     * @return string A string formatada
     */
    public static function formatDias($dias):string
    {
        $dias = str_replace(","," ",$dias);
        $dias = trim($dias);

        return $dias;
    }

    /**
     * Criptografa uma string usando AES-256-CBC
     *
     * @param string $data A string a ser criptografada
     * @return string A string criptografada
     */
    public static function encrypt($data):string
    {
        if($data){
            $first_key = base64_decode(FIRSTKEY);
            $second_key = base64_decode(SECONDKEY);    
                
            $method = "aes-256-cbc";    
            $iv_length = openssl_cipher_iv_length($method);
            $iv = openssl_random_pseudo_bytes($iv_length);
                    
            $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);    
            $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                        
            $output = base64_encode($iv.$second_encrypted.$first_encrypted);  
            
            $output = str_replace("/","@",$output);

            return $output; 
        }    
    }

    /**
     * Descriptografa uma string criptografada usando AES-256-CBC
     *
     * @param string $input A string criptografada
     * @return mixed|bool A string descriptografada ou false se falhar
     */
    public static function decrypt($input):mixed
    {  
        if($input){
            $input = str_replace("@","/",$input);
            
            $first_key = base64_decode(FIRSTKEY);
            $second_key = base64_decode(SECONDKEY);            
            $mix =  base64_decode($input);
                    
            $method = "aes-256-cbc";    
            $iv_length = openssl_cipher_iv_length($method);
                        
            $iv = substr($mix,0,$iv_length);
            $second_encrypted = substr($mix,$iv_length,64);
            $first_encrypted = substr($mix,$iv_length+64);
                        
            $data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
            $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                
            if (hash_equals($second_encrypted,$second_encrypted_new))
                return $data;
        }
        return false;
    }

    /**
     * Formata um valor monetário para o formato de moeda brasileira
     *
     * @param string $input O valor monetário
     * @return string O valor monetário formatado
     */
    public static function formatCurrency($input):string
    {
        $input = preg_replace("/[^0-9.,]/", "", $input);

        $fmt = new \NumberFormatter('pt-BR', \NumberFormatter::CURRENCY );
        return $fmt->format($input);
    }

    /**
     * Remove a formatação de moeda e retorna um valor numérico
     *
     * @param string $input O valor monetário formatado
     * @return float O valor numérico
     */
    public static function removeCurrency($input):string
    {
        return floatval(str_replace(",",".",preg_replace("/[^0-9.,]/", "", $input)));
    }

    /**
     * Gera um código aleatório baseado em bytes randômicos
     *
     * @param int $number O número de bytes para gerar o código
     * @return string O código gerado
     */
    public static function genereteCode($number):string
    {
        return strtoupper(substr(bin2hex(random_bytes($number)), 1));
    }

    /**
     * Formata um endereço IP para o formato XXX.XXX.XXX.XXX
     *
     * @param string $ip O endereço IP a ser formatado
     * @return string|bool O endereço IP formatado ou false se inválido
     */
    public static function formatarIP($ip):string
    {

        $ip = preg_replace('/\D/', '', $ip);

        $tamanho = strlen($ip);

        // Validar se o IP possui 12 dígitos
        if ($tamanho < 4 || $tamanho > 12) {
            // Se não tiver 12 dígitos, retorne false
            return false;
        }

        // Formatar o IP no formato desejado (XXX.XXX.XXX.XXX)
        return sprintf("%03d.%03d.%03d.%03d", substr($ip, 0, 3), substr($ip, 3, 3), substr($ip, 6, 3), substr($ip, 9, 3));

    }


    /**
     * Valida um número de cep para o formato XXXXX ou XXXXX-XXX
     *
     * @param string $cep O número de cep a ser validadp
     * @return bool O número de cep valido ou false se inválido
    */
    public static function validaCep($cep):bool 
    {
        if(preg_match('/^[0-9]{5,5}([- ]?[0-9]{3,3})?$/', $cep)) {
            return true;
        }
        return false;
    }

    /**
     * Valida um número de telefone para o formato (XX) XXXX-XXXX ou (XX) XXXXX-XXXX
     *
     * @param string $telefone O número de telefone a ser validadp
     * @return bool O número de telefone valido ou false se inválido
     */
    public static function validaTelefone($telefone):bool 
    {
        // Remover quaisquer caracteres que não sejam dígitos
        $telefone = preg_replace('/\D/', '', $telefone);
            
        // Verificar se o número de telefone tem o comprimento correto
        if (strlen($telefone) != 10 && strlen($telefone) != 11) {
            return false; // Retornar falso se o comprimento for inválido
        }

        return true;
    }

     /**
     * Formata um número de telefone para o formato (XX) XXXX-XXXX ou (XX) XXXXX-XXXX
     *
     * @param string $telefone O número de telefone a ser formatado
     * @return string|bool O número de telefone formatado ou false se inválido
     */
    public static function formatPhone($telefone):string|bool
    {

        if(!$telefone)
            return false;
        
        // Formatar o número de telefone no estilo desejado (XX) XXXX-XXXX
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }
}

?>