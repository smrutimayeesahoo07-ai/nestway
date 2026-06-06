<?php
// ============================================================
//  property-detail.php  –  Single Property Detail Page
//  Fetches one PG's full details from MySQL
// ============================================================
include 'db.php';

// Get property ID from URL: property-detail.php?id=1
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch property details
$sql    = "SELECT * FROM properties WHERE id = $id";
$result = mysqli_query($conn, $sql);
$pg     = mysqli_fetch_assoc($result);

if (!$pg) {
    echo "<h2 style='text-align:center;padding:60px;'>Property not found.</h2>";
    exit;
}

// Fetch all amenities for this property
$amen_sql = "SELECT a.name FROM amenities a
             JOIN property_amenities pa ON a.id = pa.amenity_id
             WHERE pa.property_id = $id";
$amenities = mysqli_query($conn, $amen_sql);

// Amenity icon map
$iconMap = [
    'WiFi'           => 'bi-wifi',
    '3 Meals/Day'    => 'bi-cup-hot-fill',
    'Air Conditioning'=> 'bi-fan',
    'TV Lounge'      => 'bi-tv-fill',
    'Power Backup'   => 'bi-lightning-charge-fill',
    '24x7 Security'  => 'bi-shield-fill-check',
    'Hot Water'      => 'bi-droplet-fill',
    'Cycle Stand'    => 'bi-bicycle',
    'Study Room'     => 'bi-book-fill',
    'Laundry'        => 'bi-washing-machine',
    'CCTV'           => 'bi-camera-video-fill',
    'Gym'            => 'bi-dumbbell',
    'Parking'        => 'bi-car-front',
    'Warden Support' => 'bi-phone-fill',
];

// Badge class
$badgeClass = match($pg['gender']) {
    'boys'  => 'badge-boys',
    'girls' => 'badge-girls',
    default => 'badge-co'
};

// Check if logged-in user already marked interest
session_start();
$alreadyInterested = false;
if (isset($_SESSION['user_id'])) {
    $uid   = (int)$_SESSION['user_id'];
    $check = mysqli_query($conn, "SELECT * FROM interested_users WHERE user_id=$uid AND property_id=$id");
    $alreadyInterested = mysqli_num_rows($check) > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NestWay – <?= htmlspecialchars($pg['name']) ?></title>
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
    .breadcrumb-wrap{background:#fff;border-bottom:1px solid var(--border);padding:12px 0;}
    .breadcrumb-item a{color:var(--accent);text-decoration:none;font-size:.88rem;}
    .breadcrumb-item.active{font-size:.88rem;color:var(--muted);}
    .gallery-main{border-radius:16px;overflow:hidden;height:420px;}
    .gallery-main img{width:100%;height:100%;object-fit:cover;transition:transform .4s;}
    .gallery-main:hover img{transform:scale(1.03);}
    .gallery-thumb-row{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px;}
    .gallery-thumb{border-radius:10px;overflow:hidden;height:90px;cursor:pointer;border:2px solid transparent;transition:border-color .2s;}
    .gallery-thumb img{width:100%;height:100%;object-fit:cover;}
    .gallery-thumb.active{border-color:var(--accent);}
    .prop-title{font-family:'Syne',sans-serif;font-size:1.9rem;font-weight:800;}
    .badge-gender-lg{padding:5px 14px;border-radius:20px;font-size:.8rem;font-weight:700;text-transform:uppercase;}
    .badge-boys{background:#dbeafe;color:#1d4ed8;}
    .badge-girls{background:#fce7f3;color:#be185d;}
    .badge-co{background:#d1fae5;color:#065f46;}
    .rating-strip{display:flex;gap:24px;flex-wrap:wrap;background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:16px 22px;margin:18px 0;}
    .rating-item{text-align:center;}
    .rating-value{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700;}
    .rating-label{font-size:.75rem;color:var(--muted);margin-top:2px;}
    .info-card{background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:24px;margin-bottom:20px;}
    .info-card-title{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--accent);display:inline-block;}
    .amenity-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;}
    .amenity-item{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:14px 10px;background:var(--light-bg);border:1.5px solid var(--border);border-radius:12px;font-size:.82rem;gap:6px;transition:border-color .2s,background .2s;}
    .amenity-item:hover{border-color:var(--accent);background:#fff5f7;}
    .amenity-item i{font-size:1.4rem;color:var(--accent);}
    .price-box{background:linear-gradient(135deg,var(--primary),#0f3460);color:#fff;border-radius:16px;padding:24px;position:sticky;top:80px;}
    .price-tag{font-family:'Syne',sans-serif;font-size:2.4rem;font-weight:800;color:var(--accent2);}
    .price-tag small{font-size:1rem;color:rgba(255,255,255,0.6);font-family:'DM Sans';font-weight:400;}
    .btn-interested{width:100%;background:var(--accent);color:#fff;border:none;border-radius:10px;padding:14px;font-size:1rem;font-weight:700;margin-top:18px;transition:background .2s;font-family:'Syne',sans-serif;cursor:pointer;}
    .btn-interested:hover{background:#c73652;}
    .btn-interested.marked{background:#065f46;}
    .btn-shortlist{width:100%;background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,0.35);border-radius:10px;padding:12px;font-size:.9rem;font-weight:600;margin-top:10px;cursor:pointer;transition:background .2s;}
    .btn-shortlist:hover{background:rgba(255,255,255,0.1);}
    .price-info-row{display:flex;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.15);padding-top:14px;margin-top:14px;font-size:.82rem;color:rgba(255,255,255,0.65);}
    .price-info-row span:last-child{color:#fff;font-weight:600;}
    .rule-list{list-style:none;padding:0;}
    .rule-list li{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);font-size:.9rem;}
    .rule-list li:last-child{border-bottom:none;}
    footer{background:var(--primary);color:rgba(255,255,255,0.6);padding:28px 0;margin-top:60px;text-align:center;font-size:.88rem;}
    footer span{color:var(--accent);}
    /* Toast notification */
    .toast-msg{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);background:var(--primary);color:#fff;padding:12px 28px;border-radius:12px;font-size:.9rem;font-weight:600;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.3);display:none;}
    .toast-msg.show{display:block;animation:slideUp .3s ease;}
    @keyframes slideUp{from{opacity:0;transform:translate(-50%,20px)}to{opacity:1;transform:translate(-50%,0)}}
    @media(max-width:768px){.gallery-main{height:260px;}.price-box{position:static;margin-top:20px;}}
  </style>
</head>
<body>

<!-- NAVBAR -->
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
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="btn btn-signup ms-2" href="signup.php">Sign Up</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- BREADCRUMB -->
<div class="breadcrumb-wrap">
  <div class="container">
    <nav><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="index.php">Listings</a></li>
      <li class="breadcrumb-item active"><?= htmlspecialchars($pg['name']) ?></li>
    </ol></nav>
  </div>
</div>

<!-- MAIN -->
<section class="py-4">
  <div class="container">
    <div class="row g-4">

      <!-- LEFT -->
      <div class="col-lg-8">

        <!-- GALLERY -->
        <div class="gallery-main">
          <img src="<?= htmlspecialchars($pg['image_url']) ?>" alt="Main" id="mainImgSrc"/>
        </div>
        <div class="gallery-thumb-row">
          <?php
          // Show 4 thumbs – use same image with different Unsplash sizes as placeholders
          $thumbs = [$pg['image_url'],
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=200&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=200&q=80',
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=200&q=80'
          ];
          foreach ($thumbs as $i => $t):
          ?>
          <div class="gallery-thumb <?= $i===0?'active':'' ?>" onclick="changeImg(this, '<?= $t ?>')">
            <img src="<?= $t ?>" alt="thumb<?= $i ?>"/>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- HEADER -->
        <div class="mt-4">
          <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
              <h1 class="prop-title"><?= htmlspecialchars($pg['name']) ?></h1>
              <div class="d-flex align-items-center gap-2 mt-1" style="color:var(--muted);font-size:.95rem;">
                <i class="bi bi-geo-alt-fill text-danger"></i>
                <?= htmlspecialchars($pg['address']) ?>
              </div>
            </div>
            <span class="badge-gender-lg <?= $badgeClass ?>"><?= ucfirst($pg['gender']) ?></span>
          </div>

          <!-- RATING STRIP -->
          <div class="rating-strip">
            <div class="rating-item">
              <div class="rating-value" style="color:var(--accent2)"><?= $pg['rating'] ?></div>
              <div style="color:var(--accent2);font-size:.85rem;">★★★★☆</div>
              <div class="rating-label">Overall</div>
            </div>
            <div class="rating-item"><div class="rating-value">4.5</div><div class="rating-label">Cleanliness</div></div>
            <div class="rating-item"><div class="rating-value">4.1</div><div class="rating-label">Food</div></div>
            <div class="rating-item"><div class="rating-value">4.6</div><div class="rating-label">Location</div></div>
            <div class="rating-item"><div class="rating-value">4.2</div><div class="rating-label">Safety</div></div>
          </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="info-card">
          <div class="info-card-title">About This PG</div>
          <p style="font-size:.95rem;color:var(--muted);line-height:1.8;"><?= nl2br(htmlspecialchars($pg['description'])) ?></p>
        </div>

        <!-- AMENITIES -->
        <div class="info-card">
          <div class="info-card-title">Amenities</div>
          <div class="amenity-grid">
            <?php
            // Re-run amenity query (pointer already used above)
            $amen2 = mysqli_query($conn, "SELECT a.name FROM amenities a JOIN property_amenities pa ON a.id=pa.amenity_id WHERE pa.property_id=$id");
            while ($am = mysqli_fetch_assoc($amen2)):
              $icon = $iconMap[$am['name']] ?? 'bi-check-circle';
            ?>
            <div class="amenity-item">
              <i class="bi <?= $icon ?>"></i>
              <?= htmlspecialchars($am['name']) ?>
            </div>
            <?php endwhile; ?>
          </div>
        </div>

        <!-- HOUSE RULES -->
        <div class="info-card">
          <div class="info-card-title">House Rules</div>
          <ul class="rule-list">
            <li><i class="bi bi-check-circle-fill text-success"></i> Gate closing time: 10:00 PM</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> No smoking or alcohol on premises</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> Visitors allowed in common area only (before 8 PM)</li>
            <li><i class="bi bi-check-circle-fill text-success"></i> One month advance deposit required</li>
            <li><i class="bi bi-x-circle-fill text-danger"></i> No loud music after 10 PM</li>
            <li><i class="bi bi-x-circle-fill text-danger"></i> Outside guests not permitted after 8 PM</li>
          </ul>
        </div>

      </div><!-- /LEFT -->

      <!-- RIGHT – PRICE BOX -->
      <div class="col-lg-4">
        <div class="price-box">
          <div style="font-size:.8rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;">Starting From</div>
          <div class="price-tag">₹<?= number_format($pg['price']) ?> <small>/month</small></div>
          <div style="font-size:.82rem;color:rgba(255,255,255,0.55);margin-top:4px;">Includes WiFi, Meals & Housekeeping</div>

          <div class="price-info-row"><span>Availability</span><span>✅ Rooms Available</span></div>
          <div class="price-info-row"><span>Owner</span><span><?= htmlspecialchars($pg['owner_name']) ?></span></div>
          <div class="price-info-row"><span>City</span><span><?= htmlspecialchars($pg['city']) ?></span></div>

          <!-- INTERESTED BUTTON -->
          <button class="btn-interested <?= $alreadyInterested ? 'marked' : '' ?>"
                  id="interestedBtn"
                  onclick="markInterested(<?= $id ?>)">
            <i class="bi <?= $alreadyInterested ? 'bi-heart-fill' : 'bi-heart' ?> me-2" id="interestedIcon"></i>
            <span id="interestedText"><?= $alreadyInterested ? 'Interest Marked ✓' : "I'm Interested" ?></span>
          </button>

          <button class="btn-shortlist" onclick="toggleShortlist(this)">
            <i class="bi bi-bookmark me-2" id="shortlistIcon"></i>
            <span id="shortlistText">Shortlist This PG</span>
          </button>

          <div style="text-align:center;margin-top:18px;font-size:.8rem;color:rgba(255,255,255,0.4);">
            <i class="bi bi-shield-check me-1"></i>Verified PG · No Brokerage
          </div>
        </div>

        <!-- CONTACT -->
        <div class="info-card mt-3">
          <div class="info-card-title">Contact Owner</div>
          <div class="d-flex align-items-center gap-3">
            <div style="width:50px;height:50px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;">
              <i class="bi bi-person-fill"></i>
            </div>
            <div>
              <div style="font-weight:700;"><?= htmlspecialchars($pg['owner_name']) ?></div>
              <div style="font-size:.8rem;color:var(--muted);">Property Owner</div>
            </div>
          </div>
          <a href="tel:<?= $pg['owner_phone'] ?>" class="btn d-block mt-3 text-white fw-bold"
             style="background:var(--primary);border-radius:10px;padding:10px;text-decoration:none;">
            <i class="bi bi-telephone-fill me-2"></i><?= $pg['owner_phone'] ?>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- TOAST -->
<div class="toast-msg" id="toastMsg">✅ Interest marked successfully!</div>

<footer><p>© 2025 <span>NestWay</span> – Built with ❤️ for Students</p></footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Gallery switcher
  function changeImg(thumb, url) {
    document.getElementById('mainImgSrc').src = url;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
  }

  // Mark Interested – calls interest.php via AJAX (Phase 4 AJAX)
  function markInterested(propertyId) {
    const btn  = document.getElementById('interestedBtn');
    const icon = document.getElementById('interestedIcon');
    const text = document.getElementById('interestedText');

    fetch('interest.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'property_id=' + propertyId
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'added') {
        btn.classList.add('marked');
        icon.className = 'bi bi-heart-fill me-2';
        text.textContent = 'Interest Marked ✓';
        showToast('✅ Interest marked!');
      } else if (data.status === 'removed') {
        btn.classList.remove('marked');
        icon.className = 'bi bi-heart me-2';
        text.textContent = "I'm Interested";
        showToast('💔 Interest removed.');
      } else if (data.status === 'login_required') {
        window.location.href = 'login.php';
      }
    });
  }

  // Shortlist toggle
  let shortlisted = false;
  function toggleShortlist(btn) {
    shortlisted = !shortlisted;
    document.getElementById('shortlistText').textContent = shortlisted ? 'Shortlisted ✓' : 'Shortlist This PG';
    document.getElementById('shortlistIcon').className   = shortlisted ? 'bi bi-bookmark-fill me-2' : 'bi bi-bookmark me-2';
    showToast(shortlisted ? '🔖 Added to shortlist!' : 'Removed from shortlist.');
  }

  // Toast helper
  function showToast(msg) {
    const t = document.getElementById('toastMsg');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
