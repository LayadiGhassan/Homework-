<?php
class BmiController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function calculateBmi($user_id, $name, $weight, $height) {
        
        if (empty($name) || !is_numeric($weight) || !is_numeric($height) || $weight <= 0 || $height <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid input values. Please enter valid data.'
            ];
        }

        if ($weight > 500) {
            return [
                'success' => false,
                'message' => 'Weight must be less than 500 kg.'
            ];
        }

        if ($height > 3) {
            return [
                'success' => false,
                'message' => 'Height must be less than 3 meters.'
            ];
        }

        
        $bmi = $weight / ($height * $height);

      
        if ($bmi < 18.5) {
            $status = "Underweight";
            $tip = "Consider a balanced diet to gain healthy weight.";
        } elseif ($bmi < 25) {
            $status = "Normal weight";
            $tip = "Great job! Maintain your healthy lifestyle.";
        } elseif ($bmi < 30) {
            $status = "Overweight";
            $tip = "Consider regular exercise to manage your weight.";
        } else {
            $status = "Obesity";
            $tip = "Consult a healthcare provider for weight management advice.";
        }

        // Save record
        $this->model->saveBmiRecord($user_id, $name, $weight, $height, $bmi, $status);

        // Get history
        $history = $this->model->getBmiHistory($user_id);

        // Return data to view
        return [
            'success' => true,
            'name' => $name,
            'bmi' => $bmi,
            'status' => $status,
            'tip' => $tip,
            'message' => "Hello, $name. Your BMI is " . number_format($bmi, 2) . ". ($status).",
            'history' => $history
        ];
    }
}
?>
