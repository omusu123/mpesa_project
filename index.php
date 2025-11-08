<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>M-Pesa Payment</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card p-4">
      <h3 class="mb-4">Lipa na M-Pesa</h3>
      
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($_GET['error']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      
      <form action="stk_initiate.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Amount (KES)</label>
          <input type="number" name="amount" class="form-control" step="0.01" min="1" required placeholder="Enter amount" />
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" required placeholder="254712345678 or 0712345678" />
          <small class="form-text text-muted">Format: 254712345678 or 0712345678</small>
        </div>
        <button type="submit" name="submit" value="submit" class="btn btn-success">Pay with M-Pesa</button>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
