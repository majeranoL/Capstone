<?php

define("FIREBASE_URL", "https://capstone-firebase-734fe-default-rtdb.firebaseio.com/audit_trail.json");

function getAuditLogs() {
    $jsonData = @file_get_contents(FIREBASE_URL);

    if ($jsonData === false) {
        return null; 
    }

    $decodedData = json_decode($jsonData, true);

    if (!is_array($decodedData)) {
        return null;
    }

    return $decodedData; 
}

$auditLogs = getAuditLogs();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

    <h2>Audit Trail Logs</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Action</th>
                <th>Details</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($auditLogs)): ?>
                <?php foreach ($auditLogs as $userId => $logs): ?>
                    <?php if (is_array($logs)): ?>
                        <?php foreach ($logs as $logId => $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($userId) ?></td>
                                <td><?= htmlspecialchars($log['action'] ?? 'Unknown Action') ?></td>
                                <td><?= htmlspecialchars($log['details'] ?? 'No Details') ?></td>
                                <td><?= isset($log['timestamp']) ? date("Y-m-d H:i:s", (int)($log['timestamp'] / 1000)) : 'No Timestamp' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No logs found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
