<?php
include '../dbcon.php';

$query = "SELECT a.id, a.user_id, m.fullname, a.check_in, a.curr_date, a.access_status 
          FROM attendance a
          JOIN members m ON a.user_id = m.user_id
          ORDER BY a.id DESC LIMIT 20";

$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status_label = ($row['access_status'] == 'OPEN') ? '<span class="label label-success">Success</span>' : '<span class="label label-important">Denied</span>';
        $time = $row['check_in'] ? date('H:i:s', strtotime($row['check_in'])) : 'N/A';
        
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['fullname']}</td>";
        echo "<td>$time</td>";
        echo "<td>{$row['curr_date']}</td>";
        echo "<td>$status_label</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>No logs found.</td></tr>";
}
?>
