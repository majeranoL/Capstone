<?php

define("FIREBASE_URL", "https://capstone-firebase-734fe-default-rtdb.firebaseio.com/audit_trail.json");

function getAuditLogs() {
    $jsonData = @file_get_contents(FIREBASE_URL);

    if ($jsonData === false) {
        return [];
    }

    $decodedData = json_decode($jsonData, true);

    if (!is_array($decodedData)) {
        return [];
    }

    $flattenedLogs = [];

    // Flatten logs to sort by timestamp
    foreach ($decodedData as $userId => $logs) {
        if (is_array($logs)) {
            foreach ($logs as $logId => $log) {
                if (isset($log['timestamp'])) {
                    $flattenedLogs[] = [
                        'userId' => $userId,
                        'action' => $log['action'] ?? 'Unknown Action',
                        'details' => $log['details'] ?? 'No Details',
                        'timestamp' => (int) $log['timestamp'],
                    ];
                }
            }
        }
    }

    // Sort logs by timestamp (oldest first)
    usort($flattenedLogs, function ($a, $b) {
        return $a['timestamp'] <=> $b['timestamp'];
    });

    return $flattenedLogs;
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

    <h2>Audit Trail Logs (Oldest First)</h2>
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
                <?php foreach ($auditLogs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['userId']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['details']) ?></td>
                        <td><?= date("Y-m-d H:i:s", (int)($log['timestamp'] / 1000)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No logs found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
