<?php
session_start();
define('FPAG', 10); // Número de filas por página


require_once 'app/helpers/util.php';
require_once 'app/config/configDB.php';
require_once 'app/models/Cliente.php';
require_once 'app/models/AccesoDatosPDO.php';
require_once 'app/controllers/crudclientes.php';

if (!isset($_SESSION['user_id'])) {
    require_once 'app/views/login.php';
    exit;
}

$midb = AccesoDatos::getModelo();
$user = $midb->getUserById($_SESSION['user_id']);
$rol = $user['rol'];

//---- PAGINACIÓN ----
$totalfilas = $midb->numClientes();
if ($totalfilas % FPAG == 0) {
    $posfin = $totalfilas - FPAG;
} else {
    $posfin = $totalfilas - $totalfilas % FPAG;
}

if (!isset($_SESSION['posini'])) {
    $_SESSION['posini'] = 0;
}
$posAux = $_SESSION['posini'];
//------------

// Ordenación
if (!isset($_SESSION['ordenacion'])) {
    $_SESSION['ordenacion'] = " ";
}

// Borro cualquier mensaje "
$_SESSION['msg'] = " ";

ob_start(); // La salida se guarda en el bufer
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Proceso las ordenes de navegación
    if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case "Primero":
                $posAux = 0;
                break;
            case "Siguiente":
                $posAux += FPAG;
                if ($posAux > $posfin) $posAux = $posfin;
                break;
            case "Anterior":
                $posAux -= FPAG;
                if ($posAux < 0) $posAux = 0;
                break;
            case "Ultimo":
                $posAux = $posfin;
        }
        $_SESSION['posini'] = $posAux;
    }

    if (isset($_GET['nav-detalles']) && isset($_GET['id'])) {
        switch ($_GET['nav-detalles']) {
            case "Siguiente":
                crudDetallesSiguiente($_GET['id']);
                break;
            case "Anterior":
                crudDetallesAnterior($_GET['id']);
                break;
        }
    }

    if (isset($_GET['nav-modificar']) && isset($_GET['id'])) {
        switch ($_GET['nav-modificar']) {
            case "Siguiente":
                crudModificarSiguiente($_GET['id']);
                break;
            case "Anterior":
                crudModificarAnterior($_GET['id']);
                break;
        }
    }

    // Proceso de ordenes de CRUD clientes
    if (isset($_GET['orden'])) {
        switch ($_GET['orden']) {
            case "Nuevo":
                crudAlta();
                break;
            case "Borrar":
                crudBorrar($_GET['id']);
                break;
            case "Modificar":
                crudModificar($_GET['id']);
                break;
            case "Detalles":
                crudDetalles($_GET['id']);
                break;
            case "Terminar":
                crudTerminar();
                break;
        }
    }

    // Ordenamiento columnas
    if (isset($_GET['ordenacion'])) {
        switch ($_GET['ordenacion']) {
            case "id-Asc":
                $_SESSION['ordenacion'] = " order by id ASC ";
                break;
            case "id-Desc":
                $_SESSION['ordenacion'] = " order by id DESC ";
                break;

            case "fname-Asc":
                $_SESSION['ordenacion'] = " order by first_name ASC ";
                break;
            case "fname-Desc":
                $_SESSION['ordenacion'] = " order by first_name DESC ";
                break;

            case "email-Asc":
                $_SESSION['ordenacion'] = " order by email ASC ";
                break;
            case "email-Desc":
                $_SESSION['ordenacion'] = " order by email DESC ";
                break;

            case "gen-Asc":
                $_SESSION['ordenacion'] = " order by gender ASC ";
                break;
            case "gen-Desc":
                $_SESSION['ordenacion'] = " order by gender DESC ";
                break;

            case "ip-Asc":
                $_SESSION['ordenacion'] = " order by ip_address ASC ";
                break;
            case "ip-Desc":
                $_SESSION['ordenacion'] = " order by ip_address DESC ";
                break;
        }
    }
}
// POST Formulario de alta o de modificación
else {
    if (isset($_POST['orden'])) {
        switch ($_POST['orden']) {
            case "Nuevo":
                crudPostAlta();
                break;
            case "Modificar":
                crudPostModificar();
                break;
            case "Detalles":; // No hago nada
            case "PDF":
                generarPDF($_POST['id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['gender'], $_POST['ip_address'], $_POST['telefono']);
                break;
        }
    }
}

// Si no hay nada en la buffer 
// Cargo genero la vista con la lista por defecto
if (ob_get_length() == 0) {
    if ($rol == 1) {
        $posini = $_SESSION['posini'];
        $tvalores = $midb->getClientes($posini, FPAG);
        require_once "app/views/list.php";
    } elseif ($rol == 0) {
        $posini = $_SESSION['posini'];
        $tvalores = $midb->getClientes($posini, FPAG);
        require_once "app/views/accesorRol0.php";
    }

}
$contenido = ob_get_clean();
$msg = $_SESSION['msg'];
// Muestro la página principal con el contenido generado
require_once "app/views/principal.php";
