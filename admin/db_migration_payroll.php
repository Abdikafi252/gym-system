<?php
include 'dbcon.php';
$sqls = [
    "ALTER TABLE staffs ADD COLUMN salary DECIMAL(12,2) DEFAULT 0.00",
    "CREATE TABLE IF NOT EXISTS payroll (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        staff_id INT NOT NULL, 
        amount DECIMAL(12,2) NOT NULL, 
        payment_date DATE NOT NULL, 
        journal_entry_id INT, 
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
        CONSTRAINT fk_payroll_staff FOREIGN KEY (staff_id) REFERENCES staffs(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($sqls as $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Success: $sql\n";
    } else {
        echo "Error: " . mysqli_error($conn) . " for query: $sql\n";
    }
}
?>
