<?php
// File: helpers/auth.php

if (!session_id()) session_start();

/**
 * Periksa apakah user sudah login dan role boleh akses backend.
 * Jika tidak, redirect ke halaman login dengan pesan error.
 */
function requireBackendAuth()
{
    // roles yang diizinkan ke backend
    $allowed = ['superuser', 'admin', 'manager'];

    // jika tidak ada session logged_in atau role tidak cocok -> redirect
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ?page=login&error=Silakan login terlebih dahulu");
        exit;
    }

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
        // opsional: logout session agar bersih
        // session_unset(); session_destroy();
        header("Location: ?page=login&error=Akses ditolak. Hanya admin/manager/superuser yang boleh masuk.");
        exit;
    }

    // jika lolos -> return true (berguna untuk test)
    return true;
}

/**
 * Fungsi utilitas untuk cek apakah current user boleh akses backend (return boolean)
 */
function isBackendUser()
{
    $allowed = ['superuser', 'admin', 'manager'];
    return (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true
        && isset($_SESSION['role']) && in_array($_SESSION['role'], $allowed));
}
