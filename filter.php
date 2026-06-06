<?php
// ============================================================
//  filter.php  –  AJAX Handler for Live Property Filtering
//  Called by JavaScript fetch() from index.php
//  Returns HTML (property cards) based on filter params
// ============================================================
include 'db.php';

// Read filter values sent via POST from JavaScript
$city   = isset($_POST['city'])   ? mysqli_real_escape_string($conn, $_POST['city'])   : '';
$budget = isset($_POST['budget']) ? (int)$_POST['budget']                              : 0;
$gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';

// Build dynamic SQL query
$sql = "SELECT * FROM properties WHERE 1=1";
if (!empty($city))   $sql .= " AND city = '$city'";
if ($budget > 0)     $sql .= " AND price <= $budget";
if (!empty($gender)) $sql .= " AND gender = '$gender'";
$sql .= " ORDER BY rating DESC";

$result = mysqli_query($conn, $sql);
$count  = mysqli_num_rows($result);

// Return count as a data attribute on a wrapper div
// so JavaScript can update the result counter
echo "<div data-count='$count'>";

if ($count === 0) {
    echo "
    <div class='col-12 text-center py-5'>
      <i class='bi bi-search display-3 text-muted'></i>
      <p class='mt-3 text-muted fs-5'>No PGs match your filters. Try adjusting them.</p>
      <button onclick='clearFilters()' class='btn mt-2'
        style='background:#e94560;color:#fff;border-radius:10px;padding:10px 24px;border:none;font-weight:600;'>
        Clear Filters
      </button>
    </div>";
} else {
    while ($pg = mysqli_fetch_assoc($result)) {
        // Fetch amenities for each property (max 4)
        $amen_sql = "SELECT a.name FROM amenities a
                     JOIN property_amenities pa ON a.id = pa.amenity_id
                     WHERE pa.property_id = {$pg['id']} LIMIT 4";
        $amen_res = mysqli_query($conn, $amen_sql);

        $badgeClass = match($pg['gender']) {
            'boys'  => 'badge-boys',
            'girls' => 'badge-girls',
            default => 'badge-co'
        };

        // Build amenity tags HTML
        $amenHTML = '';
        while ($am = mysqli_fetch_assoc($amen_res)) {
            $amenHTML .= "<span class='amenity-tag'>" . htmlspecialchars($am['name']) . "</span>";
        }

        // Output one card
        echo "
        <div class='col-xl-4 col-md-6'>
          <div class='property-card h-100'>
            <div class='card-img-wrap'>
              <img src='" . htmlspecialchars($pg['image_url']) . "'
                   alt='" . htmlspecialchars($pg['name']) . "'/>
              <span class='badge-gender $badgeClass'>" . ucfirst($pg['gender']) . "</span>
              <button class='btn-wishlist' onclick='toggleWishlist(this)'>
                <i class='bi bi-heart'></i>
              </button>
            </div>
            <div class='card-body-custom'>
              <div class='prop-name'>" . htmlspecialchars($pg['name']) . "</div>
              <div class='prop-location'>
                <i class='bi bi-geo-alt-fill text-danger'></i>
                " . htmlspecialchars($pg['city']) . "
              </div>
              <div class='prop-amenities'>$amenHTML</div>
              <hr class='prop-divider'/>
              <div class='prop-footer'>
                <div class='prop-price'>₹" . number_format($pg['price']) . " <small>/month</small></div>
                <div class='prop-rating'><i class='bi bi-star-fill'></i> {$pg['rating']}</div>
              </div>
            </div>
            <a href='property-detail.php?id={$pg['id']}' class='btn-view-details'>View Details →</a>
          </div>
        </div>";
    }
}

echo "</div>"; // close wrapper div
mysqli_close($conn);
?>
