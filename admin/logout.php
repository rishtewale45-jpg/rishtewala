<?php
require __DIR__.'/../config/config.php';
unset($_SESSION['admin']);
header('Location: login.php');
exit;
