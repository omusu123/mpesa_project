<?php
session_start();
$status = $_SESSION['transaction_status'] ?? 'No transaction status available.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Transaction Status</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card p-4 shadow-sm">
      <h4 class="mb-3">Transaction Feedback</h4>
      <p><?= htmlspecialchars($status) ?></p>
      <a href="index.php" class="btn btn-secondary">Back to Payment</a>
    </div>
  </div>
</body>
</html>
