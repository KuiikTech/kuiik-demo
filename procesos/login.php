<?php 
session_set_cookie_params(7*24*60*60);
session_start();

require_once "../clases/conexion.php";

$obj= new conectar();
$conexion=$obj->conexion();
$conexion=$obj->conexion();

$fecha_h=date('Y-m-d G:i:s');
$fecha=date('Y-m-d');

$cedula = $_POST['input_cedula'];
$contraseña = $_POST['input_contraseña'];

$contraseña = md5($contraseña);

$verificacion = 0;

$nombre = '';

$sql="SELECT `codigo`, `cedula`, `nombre`, `contraseña`, `rol`, `estado` FROM `usuarios` WHERE cedula='$cedula'";
$result=mysqli_query($conexion,$sql);
$ver=mysqli_fetch_row($result);

if ($ver == NULL)
	$verificacion = 'La cédula ingresada no se encuentra registrada';
else
{
	if($ver[5]=='ACTIVO' || $ver[4]=='Administrador')
	{
		if ($ver[3] == $contraseña)
		{
			$rol = $ver[4];
			$_SESSION['usuario_restaurante'] = $ver[0];
			$_SESSION['caja_restaurante'] = 0;

			if($rol == 'Cajero 1' || $rol == 'Cajero 2' || $rol == 'Cajero 3')
			{
				$caja = explode(' ', $rol);
				$_SESSION['caja_restaurante'] = $caja[1];
			}

			$info_registro = array(
				'Ubicación' => $_SERVER['HTTP_USER_AGENT']
			);

			$info_registro = array(
				'Tipo' => 'Inicio de Sesión',
				'Información' => $info_registro
			);

			$info_registro = json_encode($info_registro,JSON_UNESCAPED_UNICODE);

			$sql="INSERT INTO `reg_movimientos`(`descripción`, `cc_empleado`, `fecha`) VALUES (
				'$info_registro',
				'$ver[0]',
				'$fecha_h')";
			$verificacion = mysqli_query($conexion,$sql);

			$nombre = $ver[2];

			//-----------------------

			$tablet_browser = 0;
			$mobile_browser = 0;
			$body_class = 'desktop';

			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
				$tablet_browser++;
				$body_class = "tablet";
			}

			if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
				$mobile_browser++;
				$body_class = "mobile";
			}

			if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
				$mobile_browser++;
				$body_class = "mobile";
			}

			$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
			$mobile_agents = array(
				'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
				'newt','noki','palm','pana','pant','phil','play','port','prox',
				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
				'wapr','webc','winw','winw','xda ','xda-');

			if (in_array($mobile_ua,$mobile_agents))
				$mobile_browser++;

			if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0)
			{
				$mobile_browser++;
				$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua))
					$tablet_browser++;
			}
			if ($tablet_browser > 0)
				$_SESSION['browser_restaurante'] = 'Tablet';
			else if ($mobile_browser > 0)
				$_SESSION['browser_restaurante'] = 'Mobile';
			else
				$_SESSION['browser_restaurante'] = 'Desktop';
			//-----------------------
		}
		else
			$verificacion = 'La contraseña es incorrecta';
	}
	else
		$verificacion = 'El usuario ingresado se encuentra bloqueado';
}

$datos=array(
	'consulta' => $verificacion,
	'nombre' => $nombre
);

echo json_encode($datos);


?>