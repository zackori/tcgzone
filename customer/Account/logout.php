<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: /tcgzone/customer/Landing Page/index.php");
exit;
