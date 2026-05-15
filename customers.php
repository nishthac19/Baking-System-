<?php
$conn = new mysqli("localhost", "root", "", "banking_system");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$customers = $conn->query("
  SELECT c.*, cs.score, a.balance, a.account_type
  FROM customers c
  LEFT JOIN credit_score cs ON c.customer_id = cs.customer_id
  LEFT JOIN accounts a ON c.customer_id = a.customer_id
  ORDER BY cs.score DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BankIQ — Customers</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root { --bg:#0f0f13;--surface:#17171e;--border:#2a2a36;--accent:#c8a96e;--accent2:#7c6ef5;--danger:#e05252;--success:#52c97a;--text:#e8e6df;--muted:#7a7889; }
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
  .card{background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:28px;}
  table{width:100%;border-collapse:collapse;}
  th{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;text-align:left;padding:0 12px 14px;}
  td{padding:14px 12px;border-top:1px solid var(--border);font-size:14px;}
  tr:hover td{background:rgba(255,255,255,.02);}
  .score-pill{display:inline-block;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:500;}
  .score-high{background:#1a3a2a;color:var(--success);}
  .score-mid{background:#2a2a1a;color:#e0c050;}
  .score-low{background:#3a1a1a;color:var(--danger);}
  .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;text-transform:uppercase;}
  .badge.savings{background:#1a2a3a;color:#6ab0e0;}
  .badge.current{background:#2a1a3a;color:#a080e0;}
  .btn{display:inline-block;padding:10px 20px;background:var(--accent);color:#0f0f13;border-radius:10px;text-decoration:none;font-size:13px;font-weight:500;margin-bottom:24px;}
</style>
</head>
<body>
<nav>
  <div class="logo">BankIQ</div>
  <div class="nav-links">
    <a href="index.php">Dashboard</a>
    <a href="customers.php" class="active">Customers</a>
    <a href="loans.php">Loans</a>
    <a href="add_customer.php">+ New Customer</a>
  </div>
</nav>
<div class="page">
  <h1>Customers</h1>
  <p class="subtitle">All registered customers with credit scores and account info</p>
  <a href="add_customer.php" class="btn">+ Add New Customer</a>
  <div class="card">
    <table>
      <tr>
        <th>#</th><th>Name</th><th>City</th><th>Income</th>
        <th>Account Type</th><th>Balance</th><th>Credit Score</th>
      </tr>
      <?php while($r = $customers->fetch_assoc()):
        $scoreClass = $r['score'] >= 700 ? 'score-high' : ($r['score'] >= 600 ? 'score-mid' : 'score-low');
      ?>
      <tr>
        <td style="color:var(--muted)"><?= $r['customer_id'] ?></td>
        <td><strong><?= $r['name'] ?></strong><br><span style="color:var(--muted);font-size:12px"><?= $r['phone'] ?></span></td>
        <td><?= $r['city'] ?></td>
        <td>₹<?= number_format($r['annual_income']) ?></td>
        <td><span class="badge <?= $r['account_type'] ?>"><?= $r['account_type'] ?></span></td>
        <td>₹<?= number_format($r['balance']) ?></td>
        <td><span class="score-pill <?= $scoreClass ?>"><?= $r['score'] ?></span></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body>
</html>
