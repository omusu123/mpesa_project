<?php
$status = $_GET['status'] ?? 'unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment Status</title>
</head>
<body>
  <div style="margin-top: 100px; text-align: center;">
    <?php if ($status == 'waiting'): ?>
      <h2>Please check your phone to authorize payment.</h2>
    <?php else: ?>
      <h2>Unknown status. Please try again.</h2>
    <?php endif; ?>
    <a href="index.php">Back</a>
  </div>
</body>
</html>
