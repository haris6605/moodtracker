<?php
session_start();
session_unset();
session_destroy();
header("Location: t_login.html");
exit;
?>
