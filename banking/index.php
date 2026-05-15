<?php
$conn = new mysqli("localhost", "root", "", "banking_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Stats
$total_customers = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
$total_loans     = $conn->query("SELECT SUM(loan_amount) as s FROM loans WHERE status='active'")->fetch_assoc()['s'];
$defaulters      = $conn->query("SELECT COUNT(*) as c FROM loans WHERE status='defaulted'")->fetch_assoc()['c'];
$avg_score       = $conn->query("SELECT ROUND(AVG(score)) as a FROM credit_score")->fetch_assoc()['a'];

// Recent loans
$loans = $conn->query("SELECT c.name, l.loan_amount, l.status, l.interest_rate FROM loans l JOIN customers c ON l.customer_id=c.customer_id ORDER BY l.loan_id DESC LIMIT 6");

// High risk
$highrisk = $conn->query("SELECT c.name, cs.score FROM customers c JOIN credit_score cs ON c.customer_id=cs.customer_id WHERE cs.score < 600");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BankIQ — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #0f0f13;
    --surface: #17171e;
    --border: #2a2a36;
    --accent: #c8a96e;
    --accent2: #7c6ef5;
    --danger: #e05252;
    --success: #52c97a;
    --text: #e8e6df;
    --muted: #7a7889;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:var(--bg); color:var(--text); font-family:'DM Sans', sans-serif; min-height:100vh; }

  nav {
    display:flex; align-items:center; justify-content:space-between;
    padding:20px 40px; border-bottom:1px solid var(--border);
    background:var(--surface);
  }
  .logo { font-family:'DM Serif Display', serif; font-size:24px; color:var(--accent); letter-spacing:1px; }
  .nav-links { display:flex; gap:8px; }
  .nav-links a {
    color:var(--muted); text-decoration:none; padding:8px 16px;
    border-radius:8px; font-size:14px; transition:all .2s;
  }
  .nav-links a:hover, .nav-links a.active { background:var(--border); color:var(--text); }

  .page { padding:40px; max-width:1200px; margin:0 auto; }
  h1 { font-family:'DM Serif Display', serif; font-size:32px; margin-bottom:6px; }
  .subtitle { color:var(--muted); font-size:14px; margin-bottom:36px; }

  .stats { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:40px; }
  .stat-card {
    background:var(--surface); border:1px solid var(--border);
    border-radius:16px; padding:24px; position:relative; overflow:hidden;
  }
  .stat-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:var(--accent);
  }
  .stat-card.blue::before { background:var(--accent2); }
  .stat-card.red::before { background:var(--danger); }
  .stat-card.green::before { background:var(--success); }
  .stat-label { font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:12px; }
  .stat-value { font-family:'DM Serif Display', serif; font-size:34px; color:var(--text); }
  .stat-sub { font-size:12px; color:var(--muted); margin-top:4px; }

  .grid2 { display:grid; grid-template-columns:1.6fr 1fr; gap:24px; }
  .card {
    background:var(--surface); border:1px solid var(--border);
    border-radius:16px; padding:28px;
  }
  .card-title { font-size:15px; font-weight:500; margin-bottom:20px; color:var(--text); }

  table { width:100%; border-collapse:collapse; }
  th { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.8px; text-align:left; padding:0 0 12px; }
  td { padding:12px 0; border-top:1px solid var(--border); font-size:14px; }
  .badge {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:.5px;
  }
  .badge.active   { background:#1a3a2a; color:var(--success); }
  .badge.defaulted{ background:#3a1a1a; color:var(--danger); }
  .badge.closed   { background:#2a2a2a; color:var(--muted); }

  .risk-item { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-top:1px solid var(--border); }
  .risk-name { font-size:14px; }
  .risk-score { font-size:22px; font-family:'DM Serif Display',serif; color:var(--danger); }

  .btn {
    display:inline-block; padding:10px 20px; background:var(--accent);
    color:#0f0f13; border-radius:10px; text-decoration:none;
    font-size:13px; font-weight:500; transition:opacity .2s;
  }
  .btn:hover { opacity:.85; }
  .btn-outline {
    background:transparent; color:var(--accent); border:1px solid var(--accent);
    color:var(--accent);
  }
  .actions { display:flex; gap:12px; margin-bottom:36px; }
</style>
</head>
<body>
<nav>
  <div class="logo">BankIQ</div>
  <div class="nav-links">
    <a href="index.php" class="active">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="loans.php">Loans</a>
    <a href="add_customer.php">+ New Customer</a>
  </div>
</nav>

<div class="page">
  <h1>Dashboard</h1>
  <p class="subtitle">Banking Credit & Loan Analysis System</p>

  <div class="stats">
    <div class="stat-card">
      <div class="stat-label">Total Customers</div>
      <div class="stat-value"><?= $total_customers ?></div>
      <div class="stat-sub">Registered accounts</div>
    </div>
    <div class="stat-card blue">
      <div class="stat-label">Active Loan Portfolio</div>
      <div class="stat-value">₹<?= number_format($total_loans/100000,1) ?>L</div>
      <div class="stat-sub">Total disbursed</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">Defaulters</div>
      <div class="stat-value"><?= $defaulters ?></div>
      <div class="stat-sub">Loans in default</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Avg Credit Score</div>
      <div class="stat-value"><?= $avg_score ?></div>
      <div class="stat-sub">Out of 900</div>
    </div>
  </div>

  <div class="actions">
    <a href="add_customer.php" class="btn">+ Add Customer</a>
    <a href="loans.php" class="btn btn-outline">View All Loans</a>
  </div>

  <div class="grid2">
    <div class="card">
      <div class="card-title">Recent Loans</div>
      <table>
        <tr><th>Customer</th><th>Amount</th><th>Rate</th><th>Status</th></tr>
        <?php while($r = $loans->fetch_assoc()): ?>
        <tr>
          <td><?= $r['name'] ?></td>
          <td>₹<?= number_format($r['loan_amount']) ?></td>
          <td><?= $r['interest_rate'] ?>%</td>
          <td><span class="badge <?= $r['status'] ?>"><?= $r['status'] ?></span></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>

    <div class="card">
      <div class="card-title">⚠ High Risk Customers</div>
      <?php while($r = $highrisk->fetch_assoc()): ?>
      <div class="risk-item">
        <div class="risk-name"><?= $r['name'] ?></div>
        <div class="risk-score"><?= $r['score'] ?></div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
</body>
</html>
