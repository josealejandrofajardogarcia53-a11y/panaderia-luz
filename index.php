<?php
require_once "includes/auth.php";
if (estaAutenticado()) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit;
