<?php
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
$obj = new crud();

$obj = new conectar();
$conexion = $obj->conexion();

$fecha_h = date('Y-m-d G:i:s');
$response = '';

if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    $verificacion = 1;

    $sql_whatsapp_id = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Identificador WhatsApp'";
    $result_whatsapp_id = mysqli_query($conexion, $sql_whatsapp_id);
    $mostrar_whatsapp_id = mysqli_fetch_row($result_whatsapp_id);

    $whatsapp_id = $mostrar_whatsapp_id[2];

    $sql_whatsapp_token = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Token WhatsApp'";
    $result_whatsapp_token = mysqli_query($conexion, $sql_whatsapp_token);
    $mostrar_whatsapp_token = mysqli_fetch_row($result_whatsapp_token);

    $whatsapp_token = $mostrar_whatsapp_token[2];

    if ($whatsapp_token == '')
        $verificacion = 'No se ha configurado el token de WhatsApp';
    if ($whatsapp_id == '')
        $verificacion = 'No se ha configurado el identificador de WhatsApp';

    if ($verificacion == 1) {

        $url = 'https://graph.facebook.com/v16.0/' . $whatsapp_id . '/messages';

        $tipo = $_POST['tipo'];
        $cod_cliente = $_POST['cod_cliente'];
        $mensaje = $_POST['mensaje'];

        $sql_cliente = "SELECT `codigo`, `id`, `nombre`, `telefono`, `direccion`, `ciudad`, `correo`, `fecha_registro`, `tipo`, `info` FROM `clientes` WHERE codigo = '$cod_cliente'";
        $result_cliente = mysqli_query($conexion, $sql_cliente);
        $ver_cliente = mysqli_fetch_row($result_cliente);

        $telefono = $ver_cliente[3];

        if ($telefono == '')
            $verificacion = 'El cliente no tiene un número de teléfono registrado';
        else {
            if ($telefono[0] == '3')
                $telefono = '57' . $telefono;
            else
                $telefono = '57' . $telefono;
        }
        if ($verificacion == 1) {

            $info = array();
            if ($ver_cliente[9] != '')
                $info = json_decode($ver_cliente[9], true);

            if ($tipo == 'template') {
                if (isset($info['whatsapp'])) {
                    $whatsapp = $info['whatsapp'];
                    if ($whatsapp == 'verificado')
                        $verificacion = 'Cliente verificado';
                }


                //CONFIGURACION DEL MENSAJE
                $mensaje_send = ''
                    . '{'
                    . '"messaging_product": "whatsapp", '
                    . '"to": "' . $telefono . '", '
                    . '"type": "template", '
                    . '"template": '
                    . '{'
                    . '     "name": "' . $mensaje . '",'
                    . '     "language": {"code":"es"},'
                    . '} '
                    . '}';
            } else {
                $mensaje = str_replace("<br>", '\r\n', $mensaje);
                //$mensaje = "*1* - AGUILA LIGHT - *$3.000* - *$3.000* ".'\n'." *1* - CONSOMÉ - *$3.000* - *$3.000*\n";
                $mensaje_send = ''
                    . '{'
                    . '"messaging_product": "whatsapp", '
                    . '"to": "' . $telefono . '", '
                    . '"type": "text", '
                    . '"text": '
                    . '{'
                    . '     "body": "' . $mensaje . '"'
                    . '} '
                    . '}';
            }
            if ($verificacion == 1) {
                //DECLARAMOS LAS CABECERAS
                $header = array("Authorization: Bearer " . $whatsapp_token, "Content-Type: application/json",);
                //INICIAMOS EL CURL
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje_send);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                //OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
                $response = json_decode(curl_exec($curl), true);
                //IMPRIMIMOS LA RESPUESTA 
                //print_r($response);
                //OBTENEMOS EL CODIGO DE LA RESPUESTA
                $verificacion = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if($verificacion == 200)
                    $verificacion = 1;
                //CERRAMOS EL CURL
                curl_close($curl);
            }
        }
    }
} else
    $verificacion = 'Reload';
$datos = array(
    'consulta' => $verificacion,
    'response' => $response,
);

echo json_encode($datos);
