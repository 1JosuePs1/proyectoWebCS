<?php

function EscaparCorreoTemplate($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function ConstruirPlantillaRecuperacionClave($nombreUsuario, $correoUsuario, $enlaceCambio)
{
    $nombreSeguro = EscaparCorreoTemplate($nombreUsuario);
    $correoSeguro = EscaparCorreoTemplate($correoUsuario);
    $enlaceSeguro = EscaparCorreoTemplate($enlaceCambio);

    return ""
        . "<div style='margin:0;padding:24px;background:#f1f2f4;font-family:Segoe UI,Arial,sans-serif;color:#1a1a1a;'>"
        . "  <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='max-width:620px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #eceef2;box-shadow:0 8px 24px rgba(3,11,19,.08);'>"
        . "    <tr>"
        . "      <td style='background:linear-gradient(135deg,#030b13,#141f2b);padding:24px 28px;color:#ffffff;'>"
        . "        <p style='margin:0 0 8px 0;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#e5e7eb;'>My PC Gaming</p>"
        . "        <h1 style='margin:0;font-size:24px;line-height:1.2;'>Recuperar acceso</h1>"
        . "        <p style='margin:8px 0 0 0;font-size:14px;color:#cbd5e1;'>Solicitud de cambio de contrasena</p>"
        . "      </td>"
        . "    </tr>"
        . "    <tr>"
        . "      <td style='padding:28px;'>"
        . "        <p style='margin:0 0 14px 0;font-size:16px;line-height:1.6;'>Hola " . $nombreSeguro . ",</p>"
        . "        <p style='margin:0 0 20px 0;font-size:15px;line-height:1.7;color:#334155;'>Recibimos una solicitud para cambiar la contrasena de tu cuenta <strong style='color:#111827;'>" . $correoSeguro . "</strong>. Si fuiste vos, presiona este boton:</p>"
        . "        <p style='margin:0 0 24px 0;'>"
        . "          <a href='" . $enlaceSeguro . "' style='display:inline-block;background:#e42327;color:#ffffff;text-decoration:none;font-weight:700;padding:13px 22px;border-radius:10px;'>Cambiar contrasena</a>"
        . "        </p>"
        . "        <p style='margin:0 0 10px 0;font-size:13px;color:#64748b;'>Si el boton no abre, copia y pega este enlace en tu navegador:</p>"
        . "        <p style='margin:0 0 20px 0;font-size:12px;line-height:1.6;word-break:break-all;color:#0f172a;'>" . $enlaceSeguro . "</p>"
        . "        <div style='background:#fff7f7;border:1px solid #fecaca;border-radius:10px;padding:12px;font-size:12px;color:#7f1d1d;'>"
        . "          Si no solicitaste este cambio, podes ignorar este correo sin problema."
        . "        </div>"
        . "      </td>"
        . "    </tr>"
        . "    <tr>"
        . "      <td style='padding:14px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;color:#64748b;'>"
        . "        My PC Gaming"
        . "      </td>"
        . "    </tr>"
        . "  </table>"
        . "</div>";
}

function ConstruirPlantillaRecibo($lineasPedido, $usuario)
{
    $primeraLinea = $lineasPedido[0];
    $idPedido = intval($primeraLinea['idPedido'] ?? 0);
    $idVenta = intval($primeraLinea['idVenta'] ?? 0);
    $fechaPedido = EscaparCorreoTemplate($primeraLinea['fechaPedido'] ?? '');
    $estadoPedido = EscaparCorreoTemplate(ucfirst($primeraLinea['estadoPedido'] ?? 'pendiente'));
    $nombreDestinatario = EscaparCorreoTemplate($primeraLinea['nombreDestinatario'] ?? '');
    $telefonoEnvio = EscaparCorreoTemplate($primeraLinea['telefonoEnvio'] ?? '');
    $direccionEnvio = EscaparCorreoTemplate($primeraLinea['direccionEnvio'] ?? '');
    $nombreUsuario = EscaparCorreoTemplate($usuario['nombreCompleto'] ?? 'Usuario');
    $correoUsuario = EscaparCorreoTemplate($usuario['emailUsuario'] ?? '');

    $filasTabla = '';
    $totalCalculado = 0;

    foreach ($lineasPedido as $linea) {
        $cantidad = intval($linea['cantidadProductos'] ?? 0);
        $precioUnitario = floatval($linea['precioUnitarioHistorico'] ?? 0);
        $subtotal = $cantidad * $precioUnitario;
        $totalCalculado += $subtotal;

        $filasTabla .= '<tr>'
            . '<td>' . EscaparCorreoTemplate($linea['nombreProducto'] ?? '') . '</td>'
            . '<td style="text-align:center;">' . $cantidad . '</td>'
            . '<td style="text-align:right;">₡' . number_format($precioUnitario, 0, ',', '.') . '</td>'
            . '<td style="text-align:right;">₡' . number_format($subtotal, 0, ',', '.') . '</td>'
            . '</tr>';
    }

    $totalPedido = floatval($primeraLinea['totalVenta'] ?? 0);
    if ($totalPedido <= 0) {
        $totalPedido = $totalCalculado;
    }

    return '<!DOCTYPE html>'
        . '<html lang="es"><head><meta charset="UTF-8"><title>Recibo Pedido #' . $idPedido . '</title>'
        . '<style>'
        . 'body{font-family:Arial,sans-serif;margin:24px;color:#1f2937;}'
        . '.box{border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;}'
        . '.title{color:#e42327;margin:0 0 8px 0;}'
        . 'table{width:100%;border-collapse:collapse;margin-top:12px;}'
        . 'th,td{border:1px solid #e5e7eb;padding:8px;font-size:14px;}'
        . 'th{background:#f9fafb;text-align:left;}'
        . '.total{font-size:18px;font-weight:bold;color:#e42327;text-align:right;margin-top:12px;}'
        . '</style></head><body>'
        . '<h2 class="title">My Pc Gaming - Recibo de compra</h2>'
        . '<div class="box">'
        . '<p><strong>Pedido:</strong> #' . $idPedido . '</p>'
        . '<p><strong>Venta:</strong> #' . $idVenta . '</p>'
        . '<p><strong>Fecha:</strong> ' . $fechaPedido . '</p>'
        . '<p><strong>Estado:</strong> ' . $estadoPedido . '</p>'
        . '</div>'
        . '<div class="box">'
        . '<p><strong>Cliente:</strong> ' . $nombreUsuario . '</p>'
        . '<p><strong>Correo:</strong> ' . $correoUsuario . '</p>'
        . '<p><strong>Destinatario:</strong> ' . $nombreDestinatario . '</p>'
        . '<p><strong>Telefono:</strong> ' . $telefonoEnvio . '</p>'
        . '<p><strong>Direccion:</strong> ' . $direccionEnvio . '</p>'
        . '</div>'
        . '<table><thead><tr><th>Producto</th><th style="text-align:center;">Cantidad</th><th style="text-align:right;">Precio Unitario</th><th style="text-align:right;">Subtotal</th></tr></thead><tbody>'
        . $filasTabla
        . '</tbody></table>'
        . '<p class="total">Total pagado: ₡' . number_format($totalPedido, 0, ',', '.') . '</p>'
        . '</body></html>';
}
