<?php
$midb = AccesoDatos::getModelo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Verifica si se ha superado el límite de intentos
    if (isset($_SESSION['intentos']) && $_SESSION['intentos'] > 2) {
        session_destroy();

        echo "<script>alert('Se han realizado más de tres intentos fallidos. Por favor, reinicie el navegador.'); window.location.href = '/PROYECTO-CRUD-PAGINACIÓN/';</script>";
        exit;
    }

    $login = $_POST['login'];
    $password = $_POST['password'];

    $hashedPassword = hash('sha256', $password);

    $user = $midb->getUserByLogin($login);

    if ($user) {
        if ($hashedPassword === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: /PROYECTO-CRUD-PAGINACIÓN/");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos";
            // Incrementa el contador de intentos
            if (isset($_SESSION['intentos'])) {
                $_SESSION['intentos']++;
            } else {
                $_SESSION['intentos'] = 1;
            }
        }
    } else {
        $error = "Usuario o contraseña incorrectos";
        // Incrementa el contador de intentos
        if (isset($_SESSION['intentos'])) {
            $_SESSION['intentos']++;
        } else {
            $_SESSION['intentos'] = 1;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>

<body>
    <h1>Iniciar sesión</h1>
    <?php if (isset($error)) : ?>
        <p><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="login">Usuario:</label>
        <input type="text" name="login" required><br><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Iniciar sesión</button>
    </form>
</body>

</html>