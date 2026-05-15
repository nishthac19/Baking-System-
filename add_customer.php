<?php
$conn = new mysqli("localhost", "root", "", "banking_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $conn->real_escape_string($_POST['name']);
    $age     = (int)$_POST['age'];
    $income  = (float)$_POST['income'];
    $phone   = $conn->real_escape_string($_POST['phone']);
    $city    = $conn->real_escape_string($_POST['city']);
    $score   = (int)$_POST['score'];
    $acctype = $conn->real_escape_string($_POST['account_type']);
    $balance = (float)$_POST['balance'];

    // Insert customer
    $conn->query("INSERT INTO customers (name,age,annual_income,phone,city) VALUES ('$name',$age,$income,'$phone','$city')");
    $cid = $conn->insert_id;

    // Insert account
    $conn->query("INSERT INTO accounts (customer_id,account_type,balance,created_date) VALUES ($cid,'$acctype',$balance,CURDATE())");

    // Insert credit score
    $conn->query("INSERT INTO credit_score (customer_id,score,last_updated) VALUES ($cid,$score,CURDATE())");

    $approval = $score >= 650 ? "✓ Eligible for loan" : "✗ Not eligible (score < 650)";
    $success = "Customer <strong>$name</strong> added successfully! Credit score: <strong>$score</strong> — $approval";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BankIQ — Add Customer</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root{--bg:#0f0f13;--surface:#17171e;--border:#2a2a36;--accent:#c8a96e;--accent2:#7c6ef5;--danger:#e05252;--success:#52c97a;--text:#e8e6df;--muted:#7a7889;}
  *{margin:0;padding:0;box-sizing:border-box;}
  body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;}
  nav{display:flex;align-items:center;justify-content:space-between;padding:20px 40px;border-bottom:1px solid var(--border);background:var(--surface);}
  .logo{font-family:'DM Serif Display',serif;font-size:24px;color:var(--accent);}
  .nav-links{display:flex;gap:8px;}
  .nav-links a{color:var(--muted);text-decoration:none;padding:8px 16px;border-radius:8px;font-size:14px;transition:all .2s;}
  .nav-links a:hover,.nav-links a.active{background:var(--border);color:var(--text);}
  .page{padding:40px;max-width:680px;margin:0 auto;}
  h1{font-family:'DM Serif Display',serif;font-size:32px;margin-bottom:6px;}
  .subtitle{color:var(--muted);font-size:14px;margin-bottom:36px;}
  .card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:36px;}
  .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
  .form-group{display:flex;flex-direction:column;gap:8px;}
  .form-group.full{grid-column:1/-1;}
  label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;}
  input, select{
    background:#0f0f13;border:1px solid var(--border);color:var(--text);
    padding:12px 14px;border-radius:10px;font-size:14px;font-family:'DM Sans',sans-serif;
    transition:border-color .2s;outline:none;
  }
  input:focus, select:focus{border-color:var(--accent);}
  select option{background:#17171e;}
  .divider{grid-column:1/-1;border:none;border-top:1px solid var(--border);margin:8px 0;}
  .section-label{grid-column:1/-1;font-size:12px;color:var(--accent);text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
  .btn{
    grid-column:1/-1;padding:14px;background:var(--accent);color:#0f0f13;
    border:none;border-radius:10px;font-size:15px;font-weight:500;
    font-family:'DM Sans',sans-serif;cursor:pointer;transition:opacity .2s;margin-top:8px;
  }
  .btn:hover{opacity:.85;}
  .alert{padding:16px 20px;border-radius:10px;margin-bottom:24px;font-size:14px;line-height:1.6;}
  .alert.success{background:#1a3a2a;border:1px solid #2a5a3a;color:var(--success);}
  .score-hint{font-size:11px;color:var(--muted);margin-top:4px;}
</style>
</head>
<body>
<nav>
  <div class="logo">BankIQ</div>
  <div class="nav-links">
    <a href="index.php">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="loans.php">Loans</a>
    <a href="add_customer.php" class="active">+ New Customer</a>
  </div>
</nav>
<div class="page">
  <h1>Add Customer</h1>
  <p class="subtitle">Register a new customer and set their credit profile</p>

  <?php if($success): ?>
  <div class="alert success"><?= $success ?></div>
  <?php endif; ?>

  <div class="card">
    <form method="POST">
      <div class="form-grid">
        <div class="section-label">Personal Info</div>

        <div class="form-group full">
          <label>Full Name</label>
          <input type="text" name="name" required placeholder="e.g. Rahul Sharma">
        </div>
        <div class="form-group">
          <label>Age</label>
          <input type="number" name="age" required placeholder="28" min="18" max="90">
        </div>
        <div class="form-group">
          <label>City</label>
          <input type="text" name="city" required placeholder="Mumbai">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" required placeholder="9876543210">
        </div>
        <div class="form-group">
          <label>Annual Income (₹)</label>
          <input type="number" name="income" required placeholder="500000">
        </div>

        <hr class="divider">
        <div class="section-label">Account Info</div>

        <div class="form-group">
          <label>Account Type</label>
          <select name="account_type" required>
            <option value="savings">Savings</option>
            <option value="current">Current</option>
          </select>
        </div>
        <div class="form-group">
          <label>Opening Balance (₹)</label>
          <input type="number" name="balance" required placeholder="10000">
        </div>

        <hr class="divider">
        <div class="section-label">Credit Profile</div>

        <div class="form-group full">
          <label>Credit Score (300–900)</label>
          <input type="number" name="score" required placeholder="750" min="300" max="900">
          <span class="score-hint">≥ 650 = eligible for loans &nbsp;|&nbsp; &lt; 600 = high risk</span>
        </div>

        <button type="submit" class="btn">Register Customer →</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
