<?php
session_start();
require '../config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wayshare - find ride</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
  <nav>
    <a class="nav-logo" href="findRide.php">
      <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
      <span>Wayshare</span>
    </a>
    <div class="nav-center"></div>
    <div class="nav-right auth-footer" id="searchNavRight">
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </nav>

  <!-- ── MAIN LAYOUT: sidebar + content ── -->
  <div class="app-layout">

    <!-- ═══ LEFT SIDEBAR (Driver) ═══ -->
    <aside class="driver-sidebar">
      <div class="sidebar-header">
        <div class="sidebar-avatar">
            <?= strtoupper(substr($user['name'], 0, 2)) ?>
        </div>
        <div>
          <div class="sidebar-name">
            <?= $user['name'] ?>
          </div>
          <div class="sidebar-role">Driver &amp; Passenger</div>
        </div>
      </div>

      <div class="sidebar-section-label"> Panel</div>
      <nav class="sidebar-nav">
        <div class="auth-footer">
            <a class="snav-item active" href="findRide.php">

            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            Find a ride
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" onclick="showPanel('panel-post')">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Post a trip
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" onclick="showPanel('panel-edit')">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit my trip
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item" onclick="showPanel('panel-bookings')">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            View bookings
            <span class="badge">3</span>
            </a>
        </div>
        <div class="auth-footer">
            <a class="snav-item snav-danger" onclick="showPanel('panel-delete')">
            <svg viewBox="0 0 24 24" stroke-width="2" fill="none" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            Delete my trip
            </a>
        </div>
      </nav>
    </aside>

    <!-- ═══ RIGHT CONTENT PANELS ═══ -->
    <main class="panel-area">

      <!-- PANEL 1: Find a ride (search) -->
      <div id="panel-search" class="panel active">
        <div class="search-hero">
          <h1>Find your next ride</h1>
          <div class="search-bar">
            <div class="sb-field">
              <label>
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                From
              </label>
              <input type="text" placeholder="City of departure">
            </div>
            <div class="sb-field">
              <label>
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                To
              </label>
              <input type="text" placeholder="Destination">
            </div>
            <div class="sb-field">
              <label>
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Date
              </label>
              <input type="date">
            </div>
            <button class="sb-btn">
              <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
              Search
            </button>
          </div>
        </div>
        <div class="results">
          <div class="results-count">3 rides found</div>
          <div class="rides-grid">
            <div class="ride-card" style="animation-delay:0.05s">
              <div class="ride-header">
                <div class="ride-date"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Sun, Apr 19 · 7:00 AM</div>
                <div class="ride-price"><strong>$18</strong><small>per seat</small></div>
              </div>
              <div class="ride-route">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>Berkeley, CA</span><span style="color:var(--text-light)">→</span><span class="arr">Sacramento, CA</span>
              </div>
              <div class="ride-footer">
                <div class="driver-info">
                  <div class="avatar" style="background:#7c6fcd">LF</div>
                  <div><div class="driver-name">Liam Foster</div><div style="display:flex;align-items:center"><div class="stars"><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:#d0d8dc">★</span><span class="star" style="color:#d0d8dc">★</span></div><span class="review-count">(1)</span></div></div>
                </div>
                <div class="seats"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>2 seats left</div>
              </div>
              <button class="btn-book" onclick="openBooking('Berkeley, CA','Sacramento, CA','Sun, Apr 19 · 7:00 AM','Liam Foster','$18',2)">Reserve a seat</button>
            </div>
            <div class="ride-card" style="animation-delay:0.1s">
              <div class="ride-header">
                <div class="ride-date"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Mon, Apr 20 · 9:00 AM</div>
                <div class="ride-price"><strong>$45</strong><small>per seat</small></div>
              </div>
              <div class="ride-route">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>San Francisco, CA</span><span style="color:var(--text-light)">→</span><span class="arr">Los Angeles, CA</span>
              </div>
              <div class="ride-footer">
                <div class="driver-info">
                  <div class="avatar" style="background:#1a9fa0">MC</div>
                  <div><div class="driver-name">Maya Chen</div><div style="display:flex;align-items:center"><div class="stars"><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span></div><span class="review-count">(1)</span></div></div>
                </div>
                <div class="seats"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>3 seats left</div>
              </div>
              <button class="btn-book" onclick="openBooking('San Francisco, CA','Los Angeles, CA','Mon, Apr 20 · 9:00 AM','Maya Chen','$45',3)">Reserve a seat</button>
            </div>
            <div class="ride-card" style="animation-delay:0.15s">
              <div class="ride-header">
                <div class="ride-date"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Thu, Apr 23 · 6:00 AM</div>
                <div class="ride-price"><strong>$35</strong><small>per seat</small></div>
              </div>
              <div class="ride-route">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>San Jose, CA</span><span style="color:var(--text-light)">→</span><span class="arr">Lake Tahoe, CA</span>
              </div>
              <div class="ride-footer">
                <div class="driver-info">
                  <div class="avatar" style="background:#1a9fa0">MC</div>
                  <div><div class="driver-name">Maya Chen</div><div style="display:flex;align-items:center"><div class="stars"><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span><span class="star" style="color:var(--star)">★</span></div><span class="review-count">(1)</span></div></div>
                </div>
                <div class="seats"><svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>4 seats left</div>
              </div>
              <button class="btn-book" onclick="openBooking('San Jose, CA','Lake Tahoe, CA','Thu, Apr 23 · 6:00 AM','Maya Chen','$35',4)">Reserve a seat</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

</body>
</html>