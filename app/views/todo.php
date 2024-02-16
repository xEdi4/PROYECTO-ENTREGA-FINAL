<?php
echo "<h1>No hay cliente, serás redireccionado a la página principal</h1>";
?>

<meta http-equiv="refresh" content="3;url=<?= $_SERVER['PHP_SELF']; ?>">