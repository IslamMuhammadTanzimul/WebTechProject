<?php
session_start();
session_destroy();
header("Location: /WebTechProject/admin/login.php");
exit();
