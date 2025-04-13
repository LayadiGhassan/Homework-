<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'config/database.php';
require 'models/BmiModel.php';
require 'controller/BmiController.php';

$model = new BmiModel($conn);
$controller = new BmiController($model);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->calculateBmi($_SESSION['user_id'], $_POST['name'], $_POST['weight'], $_POST['height']);
    require 'views/bmi_result.php';
} else {
    require 'views/bmi_form.php';
}
?>
