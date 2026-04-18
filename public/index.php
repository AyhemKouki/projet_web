<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carpooling App</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div id="home" class="page active">
  <nav>
    <a class="nav-logo" onclick="show('home')">
      <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3m-4 12H9m10 0h-2m2 0a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2m10 0h-2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg></div>
      <span>Wayshare</span>
    </a>
    <div class="nav-center">
      <button onclick="show('search')">
        <svg width="15" height="15" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        Find a ride
      </button>
    </div>
    <div class="nav-right">
      <a class="btn-ghost" href="login.php">Log in</a>
      <a class="btn-teal" href="register.php">Sign up</a>
    </div>
  </nav>

  <section class="hero">
    <div>
      <div class="hero-badge">
        <svg width="14" height="14" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        TRAVEL TOGETHER · SPEND LESS
      </div>
      <h1>Share the ride.<br><span>Share the journey.</span></h1>
      <p class="hero-desc">Wayshare connects drivers with empty seats to passengers heading the same way. Save money, meet people, cut your carbon footprint.</p>
      <div class="hero-actions">
        <button class="btn-primary" onclick="show('search')">
          <svg width="16" height="16" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          Find a ride
        </button>
        <button class="btn-outline" onclick="show('signup')">
          Offer a ride &nbsp;→
        </button>
      </div>
    </div>
    <div class="hero-image">
      <svg viewBox="0 0 600 380" xmlns="http://www.w3.org/2000/svg">
        <!-- Background gradient -->
        <defs>
          <linearGradient id="sky" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#d4f0d0"/>
            <stop offset="50%" style="stop-color:#a8d8d5"/>
            <stop offset="100%" style="stop-color:#f5e8a0"/>
          </linearGradient>
          <linearGradient id="road" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style="stop-color:#b0c4c4"/>
            <stop offset="100%" style="stop-color:#8fa8a8"/>
          </linearGradient>
        </defs>
        <rect width="600" height="380" fill="url(#sky)"/>
        <!-- Hills -->
        <ellipse cx="150" cy="320" rx="200" ry="100" fill="#6bbf6b" opacity="0.4"/>
        <ellipse cx="480" cy="300" rx="180" ry="80" fill="#4aaa7a" opacity="0.3"/>
        <!-- Road -->
        <rect x="0" y="280" width="600" height="100" fill="url(#road)"/>
        <rect x="0" y="278" width="600" height="4" fill="#7aadad" opacity="0.5"/>
        <!-- Dashes -->
        <rect x="50" y="329" width="60" height="4" rx="2" fill="white" opacity="0.6"/>
        <rect x="160" y="329" width="60" height="4" rx="2" fill="white" opacity="0.6"/>
        <rect x="270" y="329" width="60" height="4" rx="2" fill="white" opacity="0.6"/>
        <rect x="380" y="329" width="60" height="4" rx="2" fill="white" opacity="0.6"/>
        <rect x="490" y="329" width="60" height="4" rx="2" fill="white" opacity="0.6"/>
        <!-- Car body -->
        <rect x="120" y="220" width="360" height="90" rx="20" fill="#2d8a8a"/>
        <!-- Roof -->
        <path d="M200 220 C220 170 380 170 400 220Z" fill="#246e6e"/>
        <!-- Windows -->
        <path d="M215 220 C228 182 298 178 305 220Z" fill="#a8dede" opacity="0.8"/>
        <path d="M315 220 C322 178 385 182 385 220Z" fill="#a8dede" opacity="0.8"/>
        <!-- Window divider -->
        <rect x="308" y="180" width="6" height="40" fill="#246e6e"/>
        <!-- People visible through windows -->
        <!-- Person 1 (driver) -->
        <circle cx="255" cy="195" r="18" fill="#e8a87a"/>
        <path d="M240 205 Q255 220 270 205" fill="#1a5555"/>
        <!-- Person 2 -->
        <circle cx="355" cy="193" r="17" fill="#c9785a"/>
        <path d="M340 203 Q355 218 370 203" fill="#ff6b6b"/>
        <!-- Doors -->
        <rect x="132" y="233" width="110" height="70" rx="8" fill="#256a6a"/>
        <rect x="252" y="233" width="115" height="70" rx="4" fill="#256a6a"/>
        <rect x="374" y="233" width="100" height="70" rx="8" fill="#256a6a"/>
        <!-- Door handles -->
        <rect x="185" y="265" width="24" height="5" rx="3" fill="#1a4f4f"/>
        <rect x="300" y="265" width="24" height="5" rx="3" fill="#1a4f4f"/>
        <rect x="415" y="265" width="24" height="5" rx="3" fill="#1a4f4f"/>
        <!-- Door lines -->
        <line x1="243" y1="233" x2="243" y2="303" stroke="#1a5555" stroke-width="2"/>
        <line x1="368" y1="233" x2="368" y2="303" stroke="#1a5555" stroke-width="2"/>
        <!-- Wheels -->
        <circle cx="200" cy="304" r="32" fill="#1a2b2b"/>
        <circle cx="200" cy="304" r="18" fill="#3d5a5a"/>
        <circle cx="200" cy="304" r="8" fill="#c0d0d0"/>
        <circle cx="400" cy="304" r="32" fill="#1a2b2b"/>
        <circle cx="400" cy="304" r="18" fill="#3d5a5a"/>
        <circle cx="400" cy="304" r="8" fill="#c0d0d0"/>
        <!-- Headlights -->
        <rect x="460" y="248" width="22" height="10" rx="5" fill="#ffeaa0"/>
        <!-- Tail lights -->
        <rect x="118" y="248" width="18" height="10" rx="5" fill="#ff7070"/>
        <!-- Grill -->
        <rect x="460" y="262" width="20" height="28" rx="4" fill="#1a4040"/>
      </svg>
      <div class="hero-badge-float">
        <div class="hb-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" stroke="#1a9fa0" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"/></svg>
        </div>
        <div>
          <strong>Active community</strong>
          <small>10k+ rides shared</small>
        </div>
      </div>
    </div>
  </section>

  <section class="features">
    <div class="features-grid">
      <div class="feat-card">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <h3>Save up to 75%</h3>
        <p>Split fuel and tolls with passengers heading the same way.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3>Verified profiles</h3>
        <p>Read reviews and ratings before you book or accept anyone.</p>
      </div>
      <div class="feat-card">
        <div class="feat-icon">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        </div>
        <h3>Greener trips</h3>
        <p>Fewer cars on the road means lower emissions for everyone.</p>
      </div>
    </div>
  </section>
</div>

</body>
</html>