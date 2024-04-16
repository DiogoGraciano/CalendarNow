<?php
namespace app\classes;

/**
 * Classe abstrata controllerAbstract é uma classe base para controladores.
 *
 * Esta classe fornece métodos utilitários comuns que podem ser usados por controladores específicos.
 */
abstract class controllerAbstract
{
    /**
     * @var string $url URL base do site.
     */
    public $url;

    /**
     * Construtor da classe.
     * Define a URL base do site com base no protocolo e nome do domínio.
     */
    public function __construct()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'];
        $this->url = $protocol . $domainName . "/";
    }

    /**
     * Define uma variável de sessão.
     *
     * @param string $nome Nome da variável de sessão.
     * @param mixed $valor Valor da variável de sessão.
     */
    public function setSessionVar(string $nome, $valor)
    {
        $_SESSION[$nome] = $valor;
    }

    /**
     * Obtém o valor de uma variável de sessão.
     *
     * @param string $nome Nome da variável de sessão.
     * @return mixed Valor da variável de sessão ou uma string vazia se não existir.
     */
    public function getSessionVar(string $nome)
    {
        return array_key_exists($nome, $_SESSION) ? $_SESSION[$nome] : "";
    }

    /**
     * Obtém o valor de uma variável.
     *
     * @param string $var Nome da variável.
     * @return mixed Valor da variável ou uma string vazia se não existir.
     */
    public function getValue(string $var)
    {
        if (isset($_POST[$var]))
            return $_POST[$var];
        elseif (isset($_GET[$var]))
            return $_GET[$var];
        elseif (isset($_SESSION[$var]))
            return $_SESSION[$var];
        else
            return "";
    }

    /**
     * Define os parâmetros com base nas colunas fornecidas e nos dados retornados pela API.
     *
     * @param array $columns Colunas a serem retornadas.
     * @param array $values Dados retornados pela API.
     * @return array Array contendo os valores das colunas especificadas.
     */
    public function setParameters(array $columns, array $values)
    {
        $return = [];
        foreach ($columns as $column) {
            if (isset($values[$column])) {
                $return[] = $values[$column];
            }
        }
        return $return;
    }

    /**
     * Redireciona para o caminho especificado.
     *
     * @param string $caminho Caminho para redirecionamento.
     */
    public function go(string $caminho)
    {
        echo '<meta http-equiv="refresh" content="0;url=' . $this->url . $caminho . '">';
        exit;
    }

    /**
     * Verifica se o dispositivo é móvel com base no user agent.
     *
     * @return bool Retorna true se o dispositivo for móvel, caso contrário retorna false.
     */
    public function isMobile()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        // Expressões regulares para detectar dispositivos móveis
        $regex_mobile = '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i';
        $regex_ua = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk|tcl\-|tdg\-)|t5(0(0|1)|10)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx (_)|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i';
        if (preg_match($regex_mobile, $useragent) || preg_match($regex_ua, substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }
}
