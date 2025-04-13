<?php
class BmiModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function saveBmiRecord($user_id, $name, $weight, $height, $bmi, $status) {
        $stmt = $this->db->prepare("INSERT INTO bmi_records (user_id, name, weight, height, bmi, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isfffs", $user_id, $name, $weight, $height, $bmi, $status);
        $stmt->execute();
        $stmt->close();
    }

    public function getBmiHistory($user_id) {
        $stmt = $this->db->prepare("SELECT name, bmi, status, timestamp FROM bmi_records WHERE user_id = ? ORDER BY timestamp DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'name' => $row['name'],
                'bmi' => $row['bmi'],
                'status' => $row['status'],
                'timestamp' => $row['timestamp']
            ];
        }
        $stmt->close();
        return $history;
    }
}
?>
