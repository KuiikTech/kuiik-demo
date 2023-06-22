<?php
date_default_timezone_set('America/Bogota');
setlocale(LC_TIME, 'spanish');
require_once "../clases/conexion.php";
require_once "../clases/crud.php";
require_once('../vendors/fpdf182/fpdf.php');
$obj = new crud();
$obj_2 = new conectar();
$conexion = $obj_2->conexion();
$fecha_h = date('Y-m-d G:i:s');

$cod_factura = 0;
$ruta_pdf = '';
if (isset($_SESSION['usuario_restaurante'])) {
    $usuario = $_SESSION['usuario_restaurante'];

    require_once "../clases/permisos.php";
    $obj_permisos = new permisos();
    $acceso = $obj_permisos->buscar_permiso($usuario, 'Facturas', 'CREAR');

    if ($acceso == 'SI') {
        $verificacion = 1;

        $files = glob('../pdf/*');
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }

        $cod_factura = $_POST['cod_factura'];

        $ticket = array();

        $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Ticket'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        if ($ver != null) {
            $ticket = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
            $ticket = str_replace('  ', ' ', $ticket);
            $ticket = json_decode($ticket, true);
        }

        $mensaje = array();

        $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Mensaje Ticket'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        if ($ver != null) {
            $mensaje = preg_replace("/[\r\n|\n|\r]+/", " ", $ver[2]);
            $mensaje = str_replace('  ', ' ', $mensaje);
            $mensaje = json_decode($mensaje, true);
        }

        $sql = "SELECT `codigo`, `descripcion`, `valor` FROM `configuraciones` WHERE descripcion = 'Empresa'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        $empresa = json_decode($ver[2], true);

        $sql = "SELECT `codigo`, `cliente`, `items`, `config`, `fecha_registro`, `creador`, `pagos` FROM `facturas` WHERE codigo = '$cod_factura'";
        $result = mysqli_query($conexion, $sql);
        $mostrar = mysqli_fetch_row($result);

        $total = 0;
        $pagos = array();
        if ($mostrar[6] != '')
            $pagos = json_decode($mostrar[6], true);

        $numero = '';

        $total = 0;
        $cliente = json_decode($mostrar[1], true);
        $config = json_decode($mostrar[3], true);
        if ($config['prefijo'] != '')
            $numero .= $config['prefijo'] . '-';

        $numero .= str_pad($config['numero'], 3, "0", STR_PAD_LEFT);

        if ($config['sufijo'] != '')
            $numero .= $config['sufijo'];

        $resolucion = $config['resolucion'];
        $inicio = $config['inicio'];
        $fin = $config['fin'];
        $prefijo = $config['prefijo'];
        $sufijo = $config['sufijo'];

        $fecha_resolucion = $config['fecha_resolucion'];

        $items_factura = json_decode($mostrar[2], true);
        foreach ($items_factura as $i => $producto)
            $total += $producto['valor_unitario'] * $producto['cant'];

        $creador = $mostrar[5];

        $sql_e = "SELECT nombre, rol, foto FROM usuarios WHERE codigo = '$creador'";
        $result_e = mysqli_query($conexion, $sql_e);
        $ver_e = mysqli_fetch_row($result_e);

        $creador = $ver_e[0];

        $fecha_factura = date("d/m/Y", strtotime($mostrar[4]));
        $hora_factura = date("g:i:s A", strtotime($mostrar[4]));

        class PDF extends FPDF
        {
        }

        $codigo = str_pad($cod_factura, 6, "0", STR_PAD_LEFT);
        $fecha = date('d/m/Y g:i:s A');

        $tam_caracteres = count($items_factura);

        $alto_pag = 150 + (($tam_caracteres) * 5);
        $pdf = new PDF('P', 'mm', array(70, $alto_pag));
        $pdf->AliasNbPages();
        $pdf->AddPage();

        $pos_y = 5;
        if ($ticket[1]['estado'] == 'true') {
            $pdf->AddFont('framd', '', 'framd.php');
            $pdf->Image('../recursos/logo_empresa.png', 4, 5, 60);
            $pos_y = 28;
        }
        $pdf->SetFont('framd', '', 9);

        if ($ticket[3]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode($empresa['nombre']), 0, 0, 'C');
            $pos_y += 3;
        }
        if ($ticket[4]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode("NIT: " . $empresa['nit']), 0, 0, 'C');
            $pos_y += 3;
        }
        if ($ticket[5]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode($empresa['direccion']), 0, 0, 'C');
            $pos_y += 3;
        }
        if ($ticket[7]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode($empresa['ciudad']), 0, 0, 'C');
            $pos_y += 3;
        }
        if ($ticket[6]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode($empresa['telefono']), 0, 0, 'C');
            $pos_y += 3;
        }


        $pos_y += 1;

        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

        $pdf->SetFont('framd', '', 9);
        $pos_y += 1;
        $pdf->SetXY(1, $pos_y);
        $pdf->MultiCell(68, 3, utf8_decode("RES DIAN: " . $resolucion . ' del ' . $fecha_resolucion . ' Habilitada desde ' . $prefijo . $inicio . $sufijo . ' hasta ' . $prefijo . $fin . $sufijo), 0, 'C');

        $pos_y = $pdf->GetY() + 1;

        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

        $pdf->SetFont('framd', '', 9);

        $pdf->SetXY(0, $pos_y + 1);
        $pdf->Cell(40, 5, utf8_decode("Factura # " . $numero), 0, 0, 'L');
        $pdf->SetXY(40, $pos_y);
        $pdf->Cell(30, 5, utf8_decode("Fecha: " . $fecha_factura), 0, 0, 'R');
        $pos_y += 3;
        $pdf->SetXY(40, $pos_y);
        $pdf->Cell(30, 5, utf8_decode("Hora: " . $hora_factura), 0, 0, 'R');

        $pos_y += 5;

        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);

        $pos_y += 2;
        if ($ticket[9]['estado'] == 'true') {
            $pdf->SetFont('framd', '', 9);
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode("Cliente: "), 0, 0, 'L');
            $pdf->SetFont('framd', '', 10);
            $pdf->SetXY(12, $pos_y);
            $pdf->Cell(65, 3, utf8_decode($cliente['nombre']), 0, 0, 'L');

            $pos_y += 4;
        }

        if ($ticket[10]['estado'] == 'true') {
            $pdf->SetFont('framd', '', 9);
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode("CC/Nit: "), 0, 0, 'L');
            $pdf->SetFont('framd', '', 10);
            $pdf->SetXY(10, $pos_y);
            $pdf->Cell(67, 3, utf8_decode($cliente['cedula']), 0, 0, 'L');

            $pos_y += 4;
        }

        if ($ticket[11]['estado'] == 'true') {
            $pdf->SetFont('framd', '', 9);
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode("Tel: "), 0, 0, 'L');
            $pdf->SetFont('framd', '', 10);
            $pdf->SetXY(6, $pos_y);
            $pdf->Cell(67, 3, utf8_decode($cliente['telefono']), 0, 0, 'L');

            $pos_y += 4;
        }

        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);

        $pdf->SetFont('framd', '', 9);
        $pdf->SetXY(0, $pos_y);
        $pdf->Cell(46, 4, utf8_decode("DESCRIPCION"), 0, 0, 'C', true);
        $pdf->SetXY(46, $pos_y);
        $pdf->Cell(6, 4, utf8_decode("CANT"), 0, 0, 'R', true);
        $pdf->SetXY(52, $pos_y);
        $pdf->Cell(18, 4, utf8_decode("VALOR"), 0, 0, 'C', true);

        $pos_y += 5;

        $pdf->SetTextColor(0, 0, 0);
        $total = 0;
        $impuestos = array();
        $iva = 0;

        foreach ($items_factura as $i => $item) {
            $descripcion = $item['descripcion'];
            $valor = $item['valor_unitario'];
            $cant = $item['cant'];
            $impuesto = '-';
            $total += $valor * $cant;

            $total_producto = $item['valor_unitario'] * $item['cant'];

            $valor = '$' . number_format($total_producto, 0, '.', '.');

            if (isset($item['impuesto'])) {
                $impuesto = $item['impuesto'];
                if ($impuesto < 10)
                    $calculo_imp = '1.0' . $impuesto;
                else
                    $calculo_imp = '1.' . $impuesto;
                if (isset($impuestos[$impuesto])) {
                    $impuestos[$impuesto]['iva'] += $total_producto - ($total_producto / $calculo_imp);
                    $impuestos[$impuesto]['base'] += $total_producto / $calculo_imp;
                    $impuestos[$impuesto]['total'] += $total_producto;
                } else {
                    $impuestos[$impuesto]['tipo'] = $impuesto;
                    $impuestos[$impuesto]['iva'] = $total_producto - ($total_producto / $calculo_imp);
                    $impuestos[$impuesto]['base'] = $total_producto / $calculo_imp;
                    $impuestos[$impuesto]['total'] = $total_producto;
                }
                $iva += $total_producto - ($total_producto / $calculo_imp);
            }

            $pdf->SetXY(46, $pos_y);
            $pdf->Cell(6, 3, utf8_decode($cant), 0, 0, 'C');
            $pdf->SetXY(52, $pos_y);
            $pdf->Cell(18, 3, utf8_decode($valor), 0, 0, 'R');
            $pdf->SetFont('framd', '', 9);
            $pdf->SetXY(0, $pos_y);
            $pdf->MultiCell(46, 3, utf8_decode($descripcion), 'L');

            $pos_y = $pdf->gety();
            $pos_y += 1;
        }

        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

        $pos_y += 3;

        $pdf->SetFont('framd', '', 15);
        $pdf->SetXY(0, $pos_y);
        $pdf->Cell(68, 3, utf8_decode("TOTAL: $" . number_format($total, 0, '.', '.')), 0, 0, 'R');

        $pos_y += 5;



        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

        $pos_y += 1;
        if ($ticket[14]['estado'] == 'true') {
            if ($mostrar[6] != '') {
                $pdf->SetFont('framd', '', 10);
                $pdf->SetXY(0, $pos_y);
                $pdf->Cell(68, 3, utf8_decode("Método Pago"), 0, 0, 'C');

                $pos_y += 5;

                foreach ($pagos as $i => $pago) {
                    $pdf->SetFont('framd', '', 15);
                    $pdf->SetXY(0, $pos_y);
                    $pdf->Cell(68, 3, utf8_decode($pago['tipo'] . " --> $" . number_format($pago['valor'], 0, '.', '.')), 0, 0, 'R');

                    $pos_y += 5;
                }


                $pos_y += 1;

                $pdf->SetXY(0, $pos_y);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);
            }
        }
        if ($ticket[15]['estado'] == 'true') {
            $pos_y += 1;

            $pdf->SetFont('framd', '', 10);
            $pdf->SetXY(0, $pos_y);
            $pdf->Cell(68, 3, utf8_decode("Cajero: "), 0, 0, 'L');
            $pdf->SetFont('framd', '', 9);
            $pdf->SetXY(15, $pos_y);
            $pdf->Cell(67, 3, utf8_decode($creador), 0, 0, 'L');

            $pos_y += 4;
        }

        if ($ticket[16]['estado'] == 'true') {
            $pdf->SetXY(0, $pos_y);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);

            foreach ($mensaje as $i => $msj) {
                $pos_y += 3;
                $pdf->SetFont('framd', '', 9);

                $pdf->SetXY(0, $pos_y);
                $pdf->MultiCell(68, 3, utf8_decode($msj['text']), 0, 'C');
            }

            $pos_y += 8;
        }
        $pdf->SetXY(0, $pos_y);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->Cell(70, 0.1, '', 1, 1, 'C', true);
        $pos_y += 2;
        $pdf->Image('../recursos/logo_Kuiik.jpg', 0, $pos_y, 70);

        $pdf->SetTitle(utf8_decode('Factura No ' . $numero));
        $pdf->SetAuthor('Kuiik - Desarrollo de Software');

        $pdf->Output('f', utf8_decode('../pdf/Factura No ' . $numero . '.pdf'));

        $ruta_pdf = 'pdf/Factura*No*' . $numero . '.pdf';
    } else
        $verificacion = 'Usted no tiene permisos para crear facturas';
} else
    $verificacion = 'Reload';


$datos = array(
    'consulta' => $verificacion,
    'cod_factura' => $cod_factura,
    'ruta_pdf' => $ruta_pdf
);

echo json_encode($datos);
