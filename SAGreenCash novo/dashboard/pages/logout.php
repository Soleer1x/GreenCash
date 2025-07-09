<?php
session_start();
session_destroy();
header("Location: login.php"); // Ou para index.php se preferir a tela de início
exit;
?>