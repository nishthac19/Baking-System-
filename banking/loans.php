<?php
$conn = new mysqli("localhost", "root", "", "banking_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$loans = $conn->query("
  SELECT c.name, c.city, l.*,
    ROUND(
      (l.loan_amount * (l.interest_rate/1200) * POW(1 + l.interest_rate/1200, l.tenure_months))
      / (POW(1 + l.interest_rate/1200, l.tenure_months) - 1)
    , 0) AS emi
  FROM loans l
  JOIN customers c ON l.customer_id = c.customer_id
  ORDER BY l.loan_id DESC
");

$summary = $conn->query("SELECT status, COUNT(*) as cnt, SUM(loan_amount) as total FROM loans GROUP BY status");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BankIQ — Loans</title>
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
  .page{padding:40px;max-width:1200px;margin:0 auto;}
  h1{font-family:'DM Serif Display',serif;font-size:32px;margin-bottom:6px;}
  .subtitle{color:var(--muted);font-size:14px;margin-bottom:36px;}
  .summary{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px;}
  .sum-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;}
  .sum-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
  .sum-val{font-family:'DM Serif Display',serif;font-size:28px;}
  .sum-cnt{font-size:12px;color:var(--muted);margin-top:4px;}
  .card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px;}
  table{width:100%;border-collapse:collapse;}
  th{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;text-align:left;padding:0 10px 14px;}
  td{padding:13px 10px;border-top:1px solid var(--border);font-size:14px;}
  tr:hover td{background:rgba(255,255,255,.02);}
  .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;text-transform:uppercase;}
  .badge.active{background:#1a3a2a;color:var(--success);}
  .badge.defaulted{background:#3a1a1a;color:var(--danger);}
  .badge.closed{background:#222;color:var(--muted);}
  .emi{color:var(--accent);font-weight:500;}
</style>
</head>
<body>
<nav>
  <div class="logo">BankIQ</div>
  <div class="nav-links">
    <a href="index.php">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="loans.php" class="active">Loans</a>
    <a href="add_customer.php">+ New Customer</a>
  </div>
</nav>
<div class="page">
  <h1>Loan Portfolio</h1>
  <p class="subtitle">All loans with EMI calculation and status</p>

  <div class="summary">
    <?php while($s = $summary->fetch_assoc()):
      $color = $s['status']=='active' ? 'var(--success)' : ($s['status']=='defaulted' ? 'var(--danger)' : 'var(--muted)');
    ?>
    <div class="sum-card">
      <div class="sum-label"><?= strtoupper($s['status']) ?> Loans</div>
      <div class="sum-val" style="color:<?= $color ?>">₹<?= number_format($s['total']/100000,1) ?>L</div>
      <div class="sum-cnt"><?= $s['cnt'] ?> loan(s)</div>
    </div>
    <?php endwhile; ?>
  </div>

  <div class="card">
    <table>
      <tr><th>Customer</th><th>Amount</th><th>Rate</th><th>Tenure</th><th>Monthly EMI</th><th>Start Date</th><th>Status</th></tr>
      <?php while($r = $loans->fetch_assoc()): ?>
      <tr>
        <td><strong><?= $r['name'] ?></strong><br><span style="color:var(--muted);font-size:12px"><?= $r['city'] ?></span></td>
        <td>₹<?= number_format($r['loan_amount']) ?></td>
        <td><?= $r['interest_rate'] ?>%</td>
        <td><?= $r['tenure_months'] ?> mo</td>
        <td class="emi">₹<?= number_format($r['emi']) ?></td>
        <td style="color:var(--muted)"><?= $r['start_date'] ?></td>
        <td><span class="badge <?= $r['status'] ?>"><?= $r['status'] ?></span></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
