<?php
session_start();
session_destroy();
header("Location: /WebTechProject/login.php");
exit();
