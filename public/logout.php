<?php
session_start();
session_unset();
session_destroy();
header('Location: /sprint_manager/public/login.php');
exit;
