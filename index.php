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
      <form action="stk_initiate.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" name="amount" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Phone Number (2547XXXXXXXX)</label>
          <input type="text" name="phone" class="form-control" required />
        </div>
        <button type="submit" name="submit" value="submit" class="btn btn-success">Pay</button>
      </form>
    </div>
  </div>
</body>
</html>
