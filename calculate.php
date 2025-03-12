<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$db = 'bmi_calculator';
$user = 'root'; // Replace with your MySQL username
$pass = ''; // Replace with your MySQL password
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

if (!isset($_SESSION['bmi_history'])) {
    $_SESSION['bmi_history'] = [];
}

if (isset($_POST['name'], $_POST['weight'], $_POST['height'])) {
    $name = htmlspecialchars($_POST['name']);
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);

    if ($weight <= 0 || $height <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input values. Weight and height must be greater than zero'
        ]);
        exit;
    }

    if ($weight > 500) {
        echo json_encode([
            'success' => false,
            'message' => 'Weight must be less than 500 kg.'
        ]);
        exit;
    }

    if ($height > 3) {
        echo json_encode([
            'success' => false,
            'message' => 'Height must be less than 3 meters.'
        ]);
        exit;
    }

    $bmi = $weight / ($height * $height);

    if ($bmi < 18.5) {
        $interpretation = "Underweight";
    } elseif ($bmi < 25) {
        $interpretation = "Normal weight";
    } elseif ($bmi < 30) {
        $interpretation = "Overweight";
    } else {
        $interpretation = "Obesity";
    }

    // Save to database
    $stmt = $conn->prepare("INSERT INTO bmi_records (name, weight, height, bmi, interpretation) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sfffs", $name, $weight, $height, $bmi, $interpretation);
    $stmt->execute();
    $stmt->close();

    // Fetch all records for history
    $result = $conn->query("SELECT name, bmi, interpretation, created_at FROM bmi_records ORDER BY created_at DESC");
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'name' => $row['name'],
            'bmi' => $row['bmi'],
            'interpretation' => $row['interpretation'],
            'timestamp' => $row['created_at']
        ];
    }

    $message = "Hello, $name. Your BMI is " . number_format($bmi, 2) . ". ($interpretation).";
    echo json_encode([
        'success' => true,
        'bmi' => $bmi,
        'message' => $message,
        'history' => $history
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Data not received.'
]);
$conn->close();
?>
