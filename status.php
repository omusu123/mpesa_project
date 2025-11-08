<?php
$status = $_GET['status'] ?? 'unknown';
$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment Status</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card p-4 text-center">
      <?php if ($status == 'waiting'): ?>
        <div class="mb-4">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
          </svg>
        </div>
        <h2 class="mb-3">Payment Request Sent</h2>
        <p class="text-muted mb-4">
          <?php echo !empty($message) ? htmlspecialchars($message) : 'Please check your phone to authorize the payment.'; ?>
        </p>
        <div class="alert alert-info">
          <strong>Waiting for confirmation...</strong><br>
          You will receive an M-Pesa prompt on your phone. Enter your PIN to complete the payment.
        </div>
      <?php else: ?>
        <h2 class="mb-3">Payment Status</h2>
        <p class="text-muted">Unknown status. Please try again.</p>
      <?php endif; ?>
      <div class="mt-4">
        <a href="index.php" class="btn btn-primary">Back to Payment Form</a>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
