<?php
namespace app\classes;

class functions{
    public static function getRaiz(){
        return $_SERVER['DOCUMENT_ROOT'];
    }
    public static function getUrlBase(){
        return "http://".$_SERVER['HTTP_HOST']."/";
    }
    public static function utf8_urldecode($str) {
        return mb_convert_encoding(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)),'UTF-8');
    }
    public static function onlynumber($value){
        $value = preg_replace("/[^0-9]/","", $value);
        return $value;
    }

    public static function dateTimeBd($string){
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('Y-m-d H:i:s');

        return false;
    }

    public static function dateBd($string){
        $datetime = new \DateTimeImmutable($string);
        if ($datetime !== false)
            return $datetime->format('Y-m-d');

        return false;
    }

    public static function formatCnpjCpf($value)
    {
        $CPF_LENGTH = 11;
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === $CPF_LENGTH) {
            return functions::mask($cnpj_cpf, '###.###.###-##');
        } 
        
        return functions::mask($cnpj_cpf, '##.###.###/####-##');
    }

    public static function mask($val, $mask) {
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

    public static function formatTime($time){
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

    public static function removeSecondsTime($time){
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

    public static function formatDias($dias){
        $dias = str_replace(","," ",$dias);
        $dias = trim($dias);

        return $dias;
    }

    public static function formatPhone($phone)
    {
        $formatedPhone = preg_replace('/[^0-9]/', '', $phone);
        $matches = [];
        preg_match('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', $formatedPhone, $matches);
        if ($matches) {
            return '('.$matches[1].') '.$matches[2].'-'.$matches[3];
        }

        return $phone; 
    }

    public static function encrypt($data)
    {
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

    public static function decrypt($input)
    {
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
            
        return false;
    }

    public static function formatCurrency($input)
    {
        $input = preg_replace("/[^0-9.,]/", "", $input);

        $fmt = new \NumberFormatter('pt-BR', \NumberFormatter::CURRENCY );
        return $fmt->format($input);
    }

    public static function removeCurrency($input)
    {
        return floatval(str_replace(",",".",preg_replace("/[^0-9.,]/", "", $input)));
    }

    public static function genereteCode($number){
        return strtoupper(substr(bin2hex(random_bytes($number)), 1));
    }
}

?>