<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../models/Admin.php';
require __DIR__ . '/../models/User.php';
require __DIR__ . '/../models/Ride.php';
require __DIR__ . '/../models/Booking.php';
require __DIR__ . '/../models/Avis.php';

$adminModel = new Admin($pdo);
if (!$adminModel->isLogged()) {
    header('Location: login.php');
    exit();
}

$adminName = htmlspecialchars($adminModel->getName(), ENT_QUOTES, 'UTF-8');
$adminEmail = htmlspecialchars($adminModel->getEmail(), ENT_QUOTES, 'UTF-8');
$adminPhone = htmlspecialchars($_SESSION['admin_phone'] ?? '', ENT_QUOTES, 'UTF-8');

function adminLinkClass(string $slug, string $active, bool $danger = false): string
{
    $classes = $slug === $active ? 'snav-item active' : 'snav-item';
    if ($danger) {
        $classes .= ' danger';
    }
    return $classes;
}

function renderAdminSidebar(string $activePage): void
{
    $links = [
        ['slug' => 'dashboard', 'label' => 'Dashboard', 'url' => 'dashboard.php', 'icon' => 'M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z'],
        ['slug' => 'rides', 'label' => 'Ride Management', 'url' => 'rides.php', 'icon' => 'M1 3h15v13H1V3zm15 5h4l3 3v5h-7V8zm-9 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zm13 0a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z'],
        ['slug' => 'users', 'label' => 'User Management', 'url' => 'users.php', 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2m18 0v-2a4 4 0 0 0-3-3.87m-4-12a4 4 0 1 0 0 7.75'],
        ['slug' => 'settings', 'label' => 'Settings', 'url' => 'settings.php', 'icon' => 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm9.4 7a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z'],
        ['slug' => 'logout', 'label' => 'Logout', 'url' => 'logout.php', 'icon' => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 9h12m-5-5l5 5-5 5', 'danger' => true],
    ];

    echo '<aside class="sidebar">';
    echo '<div class="sidebar-section"><div class="sidebar-section-label">Admin</div>';
    foreach ($links as $link) {
        $danger = isset($link['danger']) && $link['danger'];
        $class = adminLinkClass($link['slug'], $activePage, $danger);
        echo '<a class="' . $class . '" href="' . $link['url'] . '">';
        echo '<svg viewBox="0 0 24 24"><path d="' . htmlspecialchars($link['icon'], ENT_QUOTES, 'UTF-8') . '"/></svg>';
        echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8');
        echo '</a>';
    }
    echo '</div>';
    echo '</aside>';
}

function renderAdminTopbar(): void
{
    echo '<nav class="topbar">';
    echo '<a class="nav-logo" href="dashboard.php"><div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3m-4 12H9m10 0h-2m2 0a2 2 0 0 1 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2m10 0h-2" fill="white"/></svg></div><span>Wayshare Admin</span></a>';
    echo '</nav>';
}
