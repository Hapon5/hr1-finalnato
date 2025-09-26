<?php
// File to store recognition data
$dataFile = 'recognitions.json';

// Load existing data
$recognitions = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRecognition = [
        'from' => htmlspecialchars($_POST['from']),
        'to' => htmlspecialchars($_POST['to']),
        'message' => htmlspecialchars($_POST['message']),
        'date' => date('Y-m-d H:i:s'),
    ];

    $recognitions[] = $newRecognition;
    file_put_contents($dataFile, json_encode($recognitions, JSON_PRETTY_PRINT));
    header("Location: recognition.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🌟 Social Recognition Board</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 30px;
            margin: 0;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        form {
            background: #ffffff;
            max-width: 600px;
            margin: 0 auto 30px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1e8449;
        }

        .recognition {
            background: #fff;
            max-width: 600px;
            margin: 0 auto 20px auto;
            padding: 15px 20px;
            border-left: 6px solid #27ae60;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .recognition strong {
            color: #2c3e50;
        }

        .date {
            color: #888;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .message {
            margin-top: 10px;
            font-style: italic;
        }

        .no-recognition {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>🌟 Social Recognition Board</h1>

    <form method="POST">
        <label for="from">From:</label>
        <input type="text" name="from" id="from" required>

        <label for="to">To:</label>
        <input type="text" name="to" id="to" required>

        <label for="message">Message:</label>
        <textarea name="message" id="message" required></textarea>

        <button type="submit">Send Recognition</button>
    </form>

    <h2 style="text-align:center; color:#2c3e50;">📝 Recent Recognitions</h2>

    <?php if (empty($recognitions)): ?>
        <p class="no-recognition">No recognitions yet. Be the first to spread positivity!</p>
    <?php else: ?>
        <?php foreach (array_reverse($recognitions) as $rec): ?>
            <div class="recognition">
                <strong><?= $rec['from'] ?></strong> recognized <strong><?= $rec['to'] ?></strong>
                <div class="message">"<?= nl2br($rec['message']) ?>"</div>
                <div class="date"><?= $rec['date'] ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>
