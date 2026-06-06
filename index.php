<?php
// ============================================================
//  index.php  –  Property Listing Page with AJAX Filtering
// ============================================================
include 'db.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NestWay – Student PG Finder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root{--primary:#1a1a2e;--accent:#e94560;--accent2:#f5a623;--light-bg:#f7f7fb;--muted:#6b7280;--border:#e5e7eb;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'DM Sans',sans-serif;background:var(--light-bg);color:var(--primary);}
    .navbar{background:var(--primary);padding:14px 0;position:sticky;top:0;z-index:100;box-shadow:0 2px 20px rgba(0,0,0,0.3);}
    .navbar-brand{font-family:'Syne',sans-serif;font-weight:800;font-size:1.6rem;color:#fff!important;}
    .navbar-brand span{color:var(--accent);}
    .nav-link{color:rgba(255,255,255,0.75)!important;font-weight:500;}
    .nav-link:hover{color:#fff!important;}
    .btn-signup{background:var(--accent);color:#fff;border:none;border-radius:8px;padding:8px 20px;font-weight:600;}
    .btn-signup:hover{background:#c73652;color:#fff;}
    .hero{background:linear-gradient(135deg,var(--primary) 0%,#16213e 50%,#0f3460 100%);padding:70px 0 50px;}
    .hero-title{font-family:'Syne',sans-serif;font-size:clamp(2rem,5vw,3rem);font-weight:800;color:#fff;}
    .hero-title span{color:var(--accent);}
    .hero-sub{color:rgba(255,255,255,0.65);font-size:1rem;margin-top:10px;}
    .hero-stats{display:flex;gap:32px;margin-top:24px;flex-wrap:wrap;}
    .stat-num{font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:700;color:var(--accent2);}
    .stat-label{color:rgba(255,255,255,0.55);font-size:.8rem;}
    .filter-bar{background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.1);padding:20px 24px;margin-top:-30px;position:relative;z-index:10;}
    .filter-bar label{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:4px;display:block;}
    .filter-bar .form-select{border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;padding:10px 14px;}
    .filter-bar .form-select:focus{border-color:var(--accent);box-shadow:none;}
    .btn-search{background:var(--accent);color:#fff;border:none;border-radius:10px;padding:11px 28px;font-weight:600;width:100%;}
    .btn-search:hover{background:#c73652;}
    .btn-clear{background:transparent;color:var(--muted);border:1.5px solid var(--border);border-radius:10px;padding:10px 20px;font-weight:600;width:100%;font-size:.85rem;margin-top:6px;}
    .btn-clear:hover{border-color:var(--accent);color:var(--accent);}
    .section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
    .section-title{font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:700;}
    .section-title span{color:var(--accent);}
    .result-count{font-size:.88rem;color:var(--muted);background:var(--border);padding:4px 12px;border-radius:20px;}
    .property-card{background:#fff;border-radius:16px;overflow:hidden;border:1.5px solid var(--border);transition:transform .25s,box-shadow .25s;}
    .property-card:hover{transform:translateY(-5px);box-shadow:0 20px 40px rgba(0,0,0,0.1);}
    .card-img-wrap{position:relative;overflow:hidden;height:200px;}
    .card-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
    .property-card:hover .card-img-wrap img{transform:scale(1.06);}
    .badge-gender{position:absolute;top:12px;left:12px;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700;text-transform:uppercase;}
    .badge-boys{background:#dbeafe;color:#1d4ed8;}
    .badge-girls{background:#fce7f3;color:#be185d;}
    .badge-co{background:#d1fae5;color:#065f46;}
    .btn-wishlist{position:absolute;top:10px;right:10px;background:#fff;border:none;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:1rem;box-shadow:0 2px 8px rgba(0,0,0,0.15);color:var(--muted);cursor:pointer;}
    .btn-wishlist:hover,.btn-wishlist.active{color:var(--accent);}
    .card-body-custom{padding:18px;}
    .prop-name{font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:700;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .prop-location{font-size:.83rem;color:var(--muted);display:flex;align-items:center;gap:4px;}
    .prop-divider{border:none;border-top:1px solid var(--border);margin:12px 0;}
    .prop-footer{display:flex;align-items:center;justify-content:space-between;}
    .prop-price{font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:700;color:var(--accent);}
    .prop-price small{font-size:.75rem;color:var(--muted);font-family:'DM Sans';font-weight:400;}
    .prop-rating{display:flex;align-items:center;gap:4px;font-size:.82rem;font-weight:600;color:#92400e;background:#fef3c7;padding:3px 10px;border-radius:20px;}
    .prop-amenities{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px;}
    .amenity-tag{background:var(--light-bg);color:var(--muted);font-size:.72rem;padding:3px 9px;border-radius:20px;border:1px solid var(--border);}
    .btn-view-details{display:block;width:100%;background:var(--primary);color:#fff;text-align:center;padding:10px;border:none;font-weight:600;font-size:.88rem;text-decoration:none;transition:background .2s;}
    .btn-view-details:hover{background:var(--accent);color:#fff;}
    .spinner-wrap{display:none;text-align:center;padding:60px 0;}
    .spinner-wrap.show{display:block;}
    .spinner-border{width:3rem;height:3rem;border-width:4px;color:var(--accent);}
    @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .property-card{animation:fadeUp .4s ease both;}
    .toast-msg{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);background:var(--primary);color:#fff;padding:12px 28px;border-radius:12px;font-size:.9rem;font-weight:600;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.3);display:none;}
    .toast-msg.show{display:block;}
    footer{background:var(--primary);color:rgba(255,255,255,0.6);padding:28px 0;margin-top:60px;text-align:center;font-size:.88rem;}
    footer span{color:var(--accent);}
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Nest<span>Way</span></a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <i class="bi bi-list text-white fs-4"></i>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto gap-2 align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">Listings</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <span class="nav-link" style="color:#f5a623!important;">
              <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
            </span>
          </li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-signup ms-2" href="signup.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <h1 class="hero-title">Find Your Perfect<br><span>Student PG</span> in Minutes</h1>
    <p class="hero-sub">Browse verified PG accommodations near your college.</p>
    <div class="hero-stats">
      <div><div class="stat-num">500+</div><div class="stat-label">PGs Listed</div></div>
      <div><div class="stat-num">20+</div><div class="stat-label">Cities</div></div>
      <div><div class="stat-num">5K+</div><div class="stat-label">Students</div></div>
    </div>
  </div>
</section>

<section class="py-0">
  <div class="container">
    <div class="filter-bar">
      <div class="row g-3 align-items-end">
        <div class="col-md-3 col-sm-6">
          <label>City</label>
          <select id="cityFilter" class="form-select" onchange="applyFilters()">
            <option value="">All Cities</option>
            <?php
            $cities = mysqli_query($conn, "SELECT DISTINCT city FROM properties ORDER BY city");
            while ($c = mysqli_fetch_assoc($cities))
                echo "<option value='{$c['city']}'>{$c['city']}</option>";
            ?>
          </select>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>Max Budget</label>
          <select id="budgetFilter" class="form-select" onchange="applyFilters()">
            <option value="">Any Budget</option>
            <option value="5000">Under ₹5,000</option>
            <option value="8000">Under ₹8,000</option>
            <option value="12000">Under ₹12,000</option>
            <option value="20000">Under ₹20,000</option>
          </select>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>Gender</label>
          <select id="genderFilter" class="form-select" onchange="applyFilters()">
            <option value="">Any Gender</option>
            <option value="boys">Boys</option>
            <option value="girls">Girls</option>
            <option value="co-ed">Co-Ed</option>
          </select>
        </div>
        <div class="col-md-3 col-sm-6">
          <label>&nbsp;</label>
          <button class="btn-search" onclick="applyFilters()">
            <i class="bi bi-search me-2"></i>Search PGs
          </button>
          <button class="btn-clear" onclick="clearFilters()">
            <i class="bi bi-x-circle me-1"></i>Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 mt-2">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Available <span>PGs</span></h2>
      <span class="result-count" id="resultCount">Loading...</span>
    </div>
    <div class="spinner-wrap" id="spinnerWrap">
      <div class="spinner-border" role="status"></div>
      <p class="mt-2 text-muted" style="font-size:.9rem;">Finding PGs for you...</p>
    </div>
    <div class="row g-4" id="propertyGrid"></div>
  </div>
</section>

<div class="toast-msg" id="toastMsg"></div>
<footer><p>© 2025 <span>NestWay</span> – Built with ❤️ for Students</p></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── AJAX FILTER ──
function applyFilters() {
  const city   = document.getElementById('cityFilter').value;
  const budget = document.getElementById('budgetFilter').value;
  const gender = document.getElementById('genderFilter').value;

  document.getElementById('spinnerWrap').classList.add('show');
  document.getElementById('propertyGrid').style.opacity = '0.3';
  document.getElementById('resultCount').textContent = 'Searching...';

  const formData = new FormData();
  formData.append('city',   city);
  formData.append('budget', budget);
  formData.append('gender', gender);

  fetch('filter.php', { method: 'POST', body: formData })
    .then(r => r.text())
    .then(html => {
      const parser  = new DOMParser();
      const doc     = parser.parseFromString(html, 'text/html');
      const wrapper = doc.querySelector('[data-count]');
      const count   = wrapper ? parseInt(wrapper.getAttribute('data-count')) : 0;

      document.getElementById('propertyGrid').innerHTML = wrapper ? wrapper.innerHTML : html;
      document.getElementById('propertyGrid').style.opacity = '1';
      document.getElementById('resultCount').textContent = count + ' result' + (count !== 1 ? 's' : '');
      document.getElementById('spinnerWrap').classList.remove('show');
    })
    .catch(() => {
      document.getElementById('spinnerWrap').classList.remove('show');
      showToast('Error loading results. Please try again.');
    });
}

// ── CLEAR FILTERS ──
function clearFilters() {
  document.getElementById('cityFilter').value   = '';
  document.getElementById('budgetFilter').value = '';
  document.getElementById('genderFilter').value = '';
  applyFilters();
}

// ── WISHLIST TOGGLE ──
function toggleWishlist(btn) {
  btn.classList.toggle('active');
  const icon = btn.querySelector('i');
  if (btn.classList.contains('active')) {
    icon.className = 'bi bi-heart-fill';
    btn.style.color = '#e94560';
    showToast('Added to wishlist!');
  } else {
    icon.className = 'bi bi-heart';
    btn.style.color = '';
  }
}

// ── TOAST ──
function showToast(msg) {
  const t = document.getElementById('toastMsg');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── LOAD ON START ──
window.addEventListener('DOMContentLoaded', applyFilters);
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
