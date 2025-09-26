<?php
// File to store reports
$reportFile = 'reports.json';

// Load existing reports
$reports = file_exists($reportFile) ? json_decode(file_get_contents($reportFile), true) : [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report = [
        'employee' => htmlspecialchars($_POST['employee']),
        'incident' => htmlspecialchars($_POST['incident']),
        'date' => date('Y-m-d H:i:s'),
    ];

    $reports[] = $report;
    file_put_contents($reportFile, json_encode($reports, JSON_PRETTY_PRINT));
    header("Location: index.php"); // Redirect to prevent form resubmission
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Safety & Compliance Tracker</title>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            color: #b22222;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            background-color: #b22222;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #8b1a1a;
        }

        .report {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #b22222;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .report strong {
            display: inline-block;
            width: 100px;
            color: #444;
        }

        .no-reports {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h2>🛡️ Safety & Compliance Incident Report</h2>

    <form method="POST" action="">
        <label for="employee">Employee Name:</label>
        <input type="text" id="employee" name="employee" required>

        <label for="incident">Incident Details:</label>
        <textarea id="incident" name="incident" required></textarea>

        <button type="submit">Submit Report</button>
    </form>

    <h3>📋 Incident History</h3>

    <?php if (empty($reports)): ?>
        <p class="no-reports">No incident reports submitted yet.</p>
    <?php else: ?>
        <?php foreach (array_reverse($reports) as $report): ?>
            <div class="report">
                <p><strong>Employee:</strong> <?= $report['employee'] ?></p>
                <p><strong>Date:</strong> <?= $report['date'] ?></p>
                <p><strong>Incident:</strong><br><?= nl2br($report['incident']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
