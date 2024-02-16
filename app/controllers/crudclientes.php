<?php

function crudBorrar($id) {
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ($resu) {
        $_SESSION['msg'] = " El usuario " . $id . " ha sido eliminado.";
    } else {
        $_SESSION['msg'] = " Error al eliminar el usuario " . $id . ".";
    }
}

function crudTerminar() {
    AccesoDatos::closeModelo();
    session_destroy();
}

function crudAlta() {
    $cli = new Cliente();
    $orden = "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/detalles.php";
    }
}

function crudDetallesSiguiente($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/detalles.php";
    }
}

function crudDetallesAnterior($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/detalles.php";
    }
}

function crudModificar($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden = "Modificar";
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/formulario.php";
    }
}

function crudModificarSiguiente($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteSiguiente($id);
    $orden = "Modificar";
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/formulario.php";
    }
}

function crudModificarAnterior($id) {
    $db = AccesoDatos::getModelo();
    $cli = $db->getClienteAnterior($id);
    $orden = "Modificar";
    if ($cli === false) {
        include_once "app/views/todo.php";
    } else {
        include_once "app/views/formulario.php";
    }
}

function crudPostAlta() {
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    // !!!!!! No se controlan que los datos sean correctos 
    $cli = new Cliente();
    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    $todoOK = true;

    if (checkEmail($cli->email)) {
        $todoOK = false;
        echo "<p>El email ya existe</p>";
    }

    if (!checkIP($cli->ip_address)) {
        $todoOK = false;
        echo "<p>La IP tiene un formato incorrecto</p>";
    }

    if (!checkTel($cli->telefono)) {
        $todoOK = false;
        echo "<p>El teléfono tiene un formato incorrecto</p>";
    }

    $db = AccesoDatos::getModelo();

    if ($todoOK) {
        $db->addCliente($cli);
        $_SESSION['msg'] = "El usuario ha sido añadido";
    } else {
        $orden = "Nuevo";
        include_once "app/views/formulario.php";
    }
}

function crudPostModificar() {
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    $cli = new Cliente();

    $cli->id            = $_POST['id'];
    $cli->first_name    = $_POST['first_name'];
    $cli->last_name     = $_POST['last_name'];
    $cli->email         = $_POST['email'];
    $cli->gender        = $_POST['gender'];
    $cli->ip_address    = $_POST['ip_address'];
    $cli->telefono      = $_POST['telefono'];

    $todoOK = true;

    if (checkEmail($cli->email, $cli->id)) {
        $todoOK = false;
        echo "<p>El email ya existe</p>";
    }

    if (!checkIP($cli->ip_address)) {
        $todoOK = false;
        echo "<p>La IP tiene un formato incorrecto</p>";
    }

    if (!checkTel($cli->telefono)) {
        $todoOK = false;
        echo "<p>El teléfono tiene un formato incorrecto</p>";
    }

    $db = AccesoDatos::getModelo();

    if ($todoOK) {
        $db->modCliente($cli);
        $_SESSION['msg'] = "El usuario ha sido modificado";
    } else {
        $orden = "Modificar";
        include_once "app/views/formulario.php";
    }
}

function checkEmail($email, $id_excluir = null) {
    $db = AccesoDatos::getModelo();
    $cli = $db->buscarEmail($email);

    if ($id_excluir && $cli && $cli->id == $id_excluir) {
        return false;
    } else {
        if ($cli) {
            return true;
        } else {
            return false;
        }
    }
}

function checkIP($ip) {
    $formato = true;
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $formato = false;
    }

    return $formato;
}

function checkTel($tel) {
    $formato = true;
    $patron = "/^\d{3}-\d{3}-\d{4}$/";
    if (!preg_match($patron, $tel)) {
        $formato = false;
    }

    return $formato;
}

function foto($id) {
    $ruta1 = "app/uploads/0000000" . $id . ".jpg";
    $ruta2 = "app/uploads/000000" . $id . ".jpg";

    if (file_exists($ruta1)) {
        return  "<img src='$ruta1'>";
    } elseif (file_exists($ruta2)) {
        return  "<img src='$ruta2'>";
    } else {
        return  "<img src='https://robohash.org/$id'>";
    }
}

function codigoPais($ip) {
    $jsonIP = file_get_contents('http://ip-api.com/json/' . $ip);
    $jsonObjeto = json_decode($jsonIP);

    if (property_exists($jsonObjeto, 'countryCode') && $jsonObjeto->countryCode !== null) {
        return $jsonObjeto->countryCode;
    } else {
        return 'no disponible';
    }
}

function generarPDF($id, $first_name, $last_name, $email, $gender, $ip_address, $telefono) {
    require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->SetTitle('Cliente PDF');
    $pdf->SetSubject('Detalles del Cliente');
    $pdf->AddPage();

    $html = '
    <h1 style="text-align: center;">Detalles del Cliente</h1>
    <table border="1" style="margin: 0 auto; padding: 10px;">
      <tr>
        <td>ID:</td>
        <td>' . $id . '</td>
      </tr>
      <tr>
        <td>Nombre:</td>
        <td>' . $first_name . ' ' . $last_name . '</td>
      </tr>
      <tr>
        <td>Email:</td>
        <td>' . $email . '</td>
      </tr>
      <tr>
        <td>Género:</td>
        <td>' . $gender . '</td>
      </tr>
      <tr>
        <td>IP Address:</td>
        <td>' . $ip_address . '</td>
      </tr>
      <tr>
        <td>Teléfono:</td>
        <td>' . $telefono . '</td>
      </tr>
    </table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('cliente.pdf', 'I');
}
