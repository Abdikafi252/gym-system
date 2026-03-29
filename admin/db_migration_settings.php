<?php
include 'dbcon.php';

// New table for systemic settings if not exists
$q1 = "CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

mysqli_query($con, $q1);

// Default settings for Face Terminal
$defaults = [
    ['face_terminal_ip', '192.168.1.100', 'The local IP address of the WL-P72 / DA-T12 terminal'],
    ['face_terminal_port', '8080', 'The port used by the terminal API (usually 80, 8080, or 8090)'],
    ['face_terminal_pass', '123456', 'The administration password for the terminal API'],
    ['face_terminal_enabled', '0', 'Set to 1 to enable automatic sync during member registration']
];

foreach ($defaults as $d) {
    $key = $d[0];
    $val = $d[1];
    $desc = $d[2];
    $check = mysqli_query($con, "SELECT setting_key FROM system_settings WHERE setting_key='$key'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "INSERT INTO system_settings (setting_key, setting_value, description) VALUES ('$key', '$val', '$desc')");
    }
}

echo "Database updated successfully with System Settings.";
?>
