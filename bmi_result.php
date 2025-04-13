<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'BmiModel.php';
require_once 'BmiController.php';


$host = 'localhost';
$db = 'bmi_calculator';
$user = 'root'; 
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$model = new BmiModel($conn);
$controller = new BmiController($model);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['weight'], $_POST['height'])) {
    $name = htmlspecialchars($_POST['name']);
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    $result = $controller->calculateBmi($_SESSION['user_id'], $name, $weight, $height);
} else {
    $result = [
        'success' => false,
        'message' => 'No data submitted.',
        'history' => $model->getBmiHistory($_SESSION['user_id'])
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Result</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">BMI Result</h1>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        <?php if ($result['success']): ?>
            <div class="alert alert-<?php echo $result['bmi'] < 18.5 ? 'info' : ($result['bmi'] < 25 ? 'success' : ($result['bmi'] < 30 ? 'warning' : 'danger')); ?>">
                <?php echo $result['message']; ?>
            </div>
            <p><strong>Health Tip:</strong> <?php echo $result['tip']; ?></p>
        <?php else: ?>
            <div class="alert alert-danger">
                <?php echo $result['message']; ?>
            </div>
        <?php endif; ?>
        
        
        <div class="mt-3">
            <h3>Previous Calculations</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>BMI</th>
                        <th>Interpretation</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['history'] as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['name']); ?></td>
                            <td><?php echo number_format($entry['bmi'], 2); ?></td>
                            <td><?php echo htmlspecialchars($entry['status']); ?></td>
                            <td><?php echo $entry['timestamp']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <a href="bmi-form.php" class="btn btn-secondary mt-3">Back to Form</a>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
