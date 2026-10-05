<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Rincian Biaya - PPDB SMP Muhammadiyah Kaliwiro</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-sidebar: #0f172a;
            --sidebar-gradient-start: #0f172a;
            --sidebar-gradient-end: #1e293b;
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --accent-yellow: #fcd535;
            --accent-yellow-hover: #eab308;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* SIDEBAR STYLING */
        .sidebar {
            width: 270px;
            background: linear-gradient(180deg, #0d1b3e 0%, #162a56 60%, #101e40 100%);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .brand-logo i {
            font-size: 26px;
            color: var(--accent-yellow);
        }

        .brand-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: #ffffff;
            line-height: 1.3;
        }

        .brand-sub {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            margin-top: 2px;
        }

        .menu-section-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            color: var(--accent-yellow);
            text-transform: uppercase;
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #ffffff;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.4) 0%, rgba(37, 99, 235, 0.1) 100%);
            border-left: 4px solid #3b82f6;
        }

        .nav-link i.icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-link.active i.icon {
            color: var(--accent-yellow);
        }

        .nav-link i.arrow {
            margin-left: auto;
            font-size: 11px;
        }

        .submenu {
            list-style: none;
            padding-left: 36px;
            padding-top: 4px;
            padding-bottom: 4px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .submenu-link {
            display: block;
            padding: 9px 12px;
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            position: relative;
        }

        .submenu-link::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%);
        }

        .submenu-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        .submenu-link.active {
            color: #ffffff;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.12);
        }

        .submenu-link.active::before {
            background: var(--accent-yellow);
            box-shadow: 0 0 8px var(--accent-yellow);
        }

        .sidebar-user {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: #f59e0b;
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-logout {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .top-navbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .page-header-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-academic-year {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .content-area {
            padding: 32px;
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* HERO BANNER */
        .summary-banner {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #3730a3 100%);
            border-radius: var(--radius-xl);
            padding: 32px;
            color: #ffffff;
            margin-bottom: 32px;
            box-shadow: 0 20px 30px -10px rgba(49, 46, 129, 0.35);
        }

        .banner-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .banner-title-box h2 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .banner-title-box p {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.75);
        }

        .total-amount-display {
            text-align: right;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 14px 24px;
            border-radius: var(--radius-lg);
        }

        .total-amount-label {
            font-size: 11px;
            letter-spacing: 1px;
            color: var(--accent-yellow);
            font-weight: 800;
            margin-bottom: 4px;
        }

        .total-amount-value {
            font-size: 30px;
            font-weight: 800;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .breakdown-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: var(--radius-lg);
        }

        .breakdown-tag {
            font-size: 11px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breakdown-value {
            font-size: 22px;
            font-weight: 800;
        }

        /* DUAL COLUMNS */
        .dual-columns-container {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 28px;
            align-items: start;
        }

        .card-form-box, .card-list-box {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 28px;
            box-shadow: var(--shadow-lg);
        }

        .card-header-title {
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px;
            font-size: 13.5px;
            font-weight: 600;
            background: #f8fafc;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
            outline: none;
        }

        .form-check-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-md);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input { opacity: 0; width: 0; height: 0; }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px; width: 18px;
            left: 3px; bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }

        input:checked + .slider { background-color: #10b981; }
        input:checked + .slider:before { transform: translateX(20px); }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary-blue);
            color: #ffffff;
            border: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 24px;
        }

        .list-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 2px solid #f1f5f9;
        }

        .badge-count {
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
        }

        .list-filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-chip {
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-muted);
            cursor: pointer;
        }

        .filter-chip.active {
            background: var(--primary-blue);
            color: #ffffff;
            border-color: var(--primary-blue);
        }

        .items-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .item-card {
            background: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-name { font-size: 16px; font-weight: 800; }

        .item-meta { display: flex; align-items: center; gap: 12px; margin-top: 6px; }

        .item-amount { font-size: 15px; font-weight: 800; color: var(--primary-blue); }

        .badge-jalur {
            font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 6px;
        }
        .badge-jalur.afirmasi { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }
        .badge-jalur.reguler { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-jalur.prestasi { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-jalur.semua { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        .badge-status {
            font-size: 10px; font-weight: 800; padding: 4px 10px; border-radius: 20px;
        }
        .badge-status.aktif { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-status.nonaktif { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        .item-actions { display: flex; align-items: center; gap: 8px; }

        .btn-action {
            padding: 8px 14px; font-size: 12.5px; font-weight: 700; border-radius: var(--radius-md);
            border: 1.5px solid var(--border-color); background: #ffffff; cursor: pointer;
            display: flex; align-items: center; gap: 6px;
        }
        .btn-action.btn-edit { color: var(--primary-blue); border-color: #bfdbfe; background: #eff6ff; }
        .btn-action.btn-delete { color: #ef4444; border-color: #fca5a5; background: #fef2f2; }

        /* MODAL EDIT */
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center; z-index: 1000;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-card {
            background: #ffffff; width: 100%; max-width: 480px;
            border-radius: var(--radius-xl); overflow: hidden;
        }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .modal-title { font-size: 17px; font-weight: 800; }
        .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
        .modal-body { padding: 24px; }
        .modal-footer { padding: 18px 24px; background: #f8fafc; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; }
        .btn-secondary { padding: 10px 18px; background: #ffffff; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 700; border-radius: var(--radius-md); cursor: pointer; }
        .btn-primary-dark { padding: 10px 20px; background: #0f172a; color: #ffffff; border: none; font-size: 13px; font-weight: 700; border-radius: var(--radius-md); cursor: pointer; }

        /* TOAST */
        .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 2000; display: flex; flex-direction: column; gap: 10px; }
        .toast { background: #0f172a; color: #ffffff; padding: 14px 20px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 12px; font-size: 13.5px; font-weight: 600; min-width: 300px; border-left: 4px solid var(--primary-blue); }
        .toast.success { border-left-color: #10b981; }
        .toast.danger { border-left-color: #ef4444; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="brand-name">SMP Muhammadiyah<br>Kaliwiro</div>
            <div class="brand-sub">Sistem Informasi PPDB</div>
        </div>

        <div class="menu-section-label">Main Menu</div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a class="nav-link"><i class="fa-solid fa-chart-pie icon"></i><span>Dashboard Keuangan</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link"><i class="fa-solid fa-receipt icon"></i><span>Verifikasi Pembayaran</span></a>
            </li>
            <li class="nav-item open">
                <a class="nav-link active"><i class="fa-solid fa-sliders icon"></i><span>Pengaturan</span><i class="fa-solid fa-chevron-down arrow"></i></a>
                <ul class="submenu">
                    <li><a class="submenu-link">Konfigurasi Keuangan</a></li>
                    <li><a class="submenu-link active">Rincian Biaya Item</a></li>
                </ul>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="user-card">
                <div class="user-avatar">B</div>
                <div class="user-info">
                    <div class="user-name">Bendahara PPDB</div>
                    <div class="user-role">SMP Muhammadiyah</div>
                </div>
            </div>
            <button class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar Panel</span></button>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="top-navbar">
            <div>
                <h1 class="page-header-title"><i class="fa-solid fa-file-invoice-dollar"></i> Kelola Rincian Biaya</h1>
                <div class="breadcrumb"><a href="#">Pengaturan</a> / <span>Rincian Biaya Item</span></div>
            </div>
            <div class="top-nav-actions">
                <div class="badge-academic-year"><i class="fa-solid fa-calendar-days"></i> T.A. 2026/2027</div>
            </div>
        </header>

        <main class="content-area">
            <section class="summary-banner">
                <div class="banner-header">
                    <div class="banner-title-box">
                        <h2><i class="fa-solid fa-calculator" style="color: var(--accent-yellow);"></i> Total Biaya Daftar Ulang</h2>
                        <p>Dihitung otomatis dari penjumlahan seluruh rincian komponen biaya yang berstatus aktif.</p>
                    </div>
                    <div class="total-amount-display">
                        <div class="total-amount-label">ESTIMASI TOTAL MAKSIMAL</div>
                        <div class="total-amount-value" id="displayTotalMax">Rp 525.000</div>
                    </div>
                </div>

                <div class="breakdown-grid">
                    <div class="breakdown-card">
                        <div class="breakdown-tag"><i class="fa-solid fa-user-graduate"></i> JALUR REGULER</div>
                        <div class="breakdown-value" id="displayReguler">Rp 450.000</div>
                    </div>
                    <div class="breakdown-card">
                        <div class="breakdown-tag"><i class="fa-solid fa-award"></i> JALUR PRESTASI</div>
                        <div class="breakdown-value" id="displayPrestasi">Rp 300.000</div>
                    </div>
                    <div class="breakdown-card">
                        <div class="breakdown-tag"><i class="fa-solid fa-hand-holding-heart"></i> JALUR AFIRMASI/BEASISWA</div>
                        <div class="breakdown-value" id="displayAfirmasi">Rp 375.000</div>
                    </div>
                </div>
            </section>

            <div class="dual-columns-container">
                <section class="card-form-box">
                    <h3 class="card-header-title"><i class="fa-solid fa-circle-plus"></i> Tambah Rincian Baru</h3>
                    <form id="addFeeForm" onsubmit="handleAddItem(event)">
                        <div class="form-group">
                            <label class="form-label" for="itemName">NAMA ITEM BIAYA</label>
                            <input type="text" id="itemName" class="form-control" placeholder="Misal: Seragam Olahraga" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="itemAmount">NOMINAL BIAYA (RP)</label>
                            <input type="number" id="itemAmount" class="form-control" placeholder="Rp 0" min="0" step="1000" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="itemJalur">TARGET JALUR PENDAFTARAN</label>
                            <select id="itemJalur" class="form-control" required>
                                <option value="semua">Semua Jalur Pendaftaran</option>
                                <option value="reguler">Jalur Reguler</option>
                                <option value="prestasi">Jalur Prestasi</option>
                                <option value="afirmasi">Jalur Afirmasi / Beasiswa</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">STATUS ITEM</label>
                            <div class="form-check-group">
                                <span style="font-size: 13px; font-weight: 600;">Langsung Aktifkan</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="itemStatus" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Item Biaya</button>
                    </form>
                </section>

                <section class="card-list-box">
                    <div class="list-header-row">
                        <div class="list-title-wrap">
                            <h3 class="list-title">Daftar Rincian Biaya</h3>
                            <span class="badge-count" id="itemCountBadge">3 ITEM</span>
                        </div>
                    </div>

                    <div class="list-filter-bar">
                        <button class="filter-chip active" onclick="filterItems('semua', this)">Semua Jalur</button>
                        <button class="filter-chip" onclick="filterItems('reguler', this)">Reguler</button>
                        <button class="filter-chip" onclick="filterItems('prestasi', this)">Prestasi</button>
                        <button class="filter-chip" onclick="filterItems('afirmasi', this)">Afirmasi</button>
                    </div>

                    <div class="items-list" id="feeItemsContainer"></div>
                </section>
            </div>
        </main>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Rincian Biaya</div>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editItemId">
                <div class="form-group">
                    <label class="form-label" for="editItemName">NAMA ITEM BIAYA</label>
                    <input type="text" id="editItemName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editItemAmount">NOMINAL BIAYA (RP)</label>
                    <input type="number" id="editItemAmount" class="form-control" min="0" step="1000" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="editItemJalur">TARGET JALUR</label>
                    <select id="editItemJalur" class="form-control" required>
                        <option value="semua">Semua Jalur Pendaftaran</option>
                        <option value="reguler">Jalur Reguler</option>
                        <option value="prestasi">Jalur Prestasi</option>
                        <option value="afirmasi">Jalur Afirmasi / Beasiswa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">STATUS ITEM</label>
                    <div class="form-check-group">
                        <span style="font-size: 13px; font-weight: 600;">Status Aktif</span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="editItemStatus">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeEditModal()">Batal</button>
                <button class="btn-primary-dark" onclick="saveEditedItem()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        let feeItems = [
            { id: 1, name: 'SPP Bulan Pertama', amount: 75000, jalur: 'afirmasi', active: true },
            { id: 2, name: 'Seragam Batik & Olahraga', amount: 150000, jalur: 'semua', active: true },
            { id: 3, name: 'Uang Gedung & Sarpras', amount: 300000, jalur: 'reguler', active: true }
        ];

        let currentFilter = 'semua';

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }

        function renderItems() {
            const container = document.getElementById('feeItemsContainer');
            container.innerHTML = '';

            const filtered = feeItems.filter(item => {
                if (currentFilter === 'semua') return true;
                return item.jalur === 'semua' || item.jalur === currentFilter;
            });

            document.getElementById('itemCountBadge').innerText = `${feeItems.length} ITEM`;

            filtered.forEach(item => {
                const card = document.createElement('div');
                card.className = 'item-card';
                let jalurLabel = ''; let jalurClass = '';
                if (item.jalur === 'afirmasi') { jalurLabel = 'Jalur Afirmasi'; jalurClass = 'afirmasi'; }
                else if (item.jalur === 'reguler') { jalurLabel = 'Jalur Reguler'; jalurClass = 'reguler'; }
                else if (item.jalur === 'prestasi') { jalurLabel = 'Jalur Prestasi'; jalurClass = 'prestasi'; }
                else { jalurLabel = 'Semua Jalur'; jalurClass = 'semua'; }

                card.innerHTML = `
                    <div class="item-main-info">
                        <div class="item-name">${item.name}</div>
                        <div class="item-meta">
                            <span class="item-amount">${formatRupiah(item.amount)}</span>
                            <span class="badge-jalur ${jalurClass}">${jalurLabel}</span>
                            <span class="badge-status ${item.active ? 'aktif' : 'nonaktif'}">
                                <i class="fa-solid ${item.active ? 'fa-check' : 'fa-xmark'}"></i>
                                ${item.active ? 'Aktif' : 'Non-aktif'}
                            </span>
                        </div>
                    </div>
                    <div class="item-actions">
                        <label class="toggle-switch" style="margin-right: 6px;">
                            <input type="checkbox" ${item.active ? 'checked' : ''} onchange="toggleItemStatus(${item.id})">
                            <span class="slider"></span>
                        </label>
                        <button class="btn-action btn-edit" onclick="openEditModal(${item.id})"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                        <button class="btn-action btn-delete" onclick="deleteItem(${item.id})"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </div>
                `;
                container.appendChild(card);
            });

            calculateTotals();
        }

        function calculateTotals() {
            let totalReguler = 0; let totalPrestasi = 0; let totalAfirmasi = 0;
            feeItems.forEach(item => {
                if (!item.active) return;
                if (item.jalur === 'semua') { totalReguler += item.amount; totalPrestasi += item.amount; totalAfirmasi += item.amount; }
                else if (item.jalur === 'reguler') { totalReguler += item.amount; }
                else if (item.jalur === 'prestasi') { totalPrestasi += item.amount; }
                else if (item.jalur === 'afirmasi') { totalAfirmasi += item.amount; }
            });
            const maxTotal = Math.max(totalReguler, totalPrestasi, totalAfirmasi);
            document.getElementById('displayReguler').innerText = formatRupiah(totalReguler);
            document.getElementById('displayPrestasi').innerText = formatRupiah(totalPrestasi);
            document.getElementById('displayAfirmasi').innerText = formatRupiah(totalAfirmasi);
            document.getElementById('displayTotalMax').innerText = formatRupiah(maxTotal);
        }

        function handleAddItem(e) {
            e.preventDefault();
            const name = document.getElementById('itemName').value;
            const amount = parseFloat(document.getElementById('itemAmount').value);
            const jalur = document.getElementById('itemJalur').value;
            const active = document.getElementById('itemStatus').checked;
            feeItems.push({ id: Date.now(), name, amount, jalur, active });
            renderItems();
            document.getElementById('addFeeForm').reset();
            document.getElementById('itemStatus').checked = true;
            showToast(`Berhasil menambahkan item "${name}"`, 'success');
        }

        function toggleItemStatus(id) {
            const item = feeItems.find(i => i.id === id);
            if (item) { item.active = !item.active; renderItems(); showToast(`Status "${item.name}" diubah`, 'info'); }
        }

        function deleteItem(id) {
            const item = feeItems.find(i => i.id === id);
            if (confirm(`Hapus "${item.name}"?`)) { feeItems = feeItems.filter(i => i.id !== id); renderItems(); showToast(`Item "${item.name}" dihapus`, 'danger'); }
        }

        function filterItems(jalur, element) {
            currentFilter = jalur;
            document.querySelectorAll('.filter-chip').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            renderItems();
        }

        function openEditModal(id) {
            const item = feeItems.find(i => i.id === id);
            if (!item) return;
            document.getElementById('editItemId').value = item.id;
            document.getElementById('editItemName').value = item.name;
            document.getElementById('editItemAmount').value = item.amount;
            document.getElementById('editItemJalur').value = item.jalur;
            document.getElementById('editItemStatus').checked = item.active;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() { document.getElementById('editModal').classList.remove('active'); }

        function saveEditedItem() {
            const id = parseInt(document.getElementById('editItemId').value);
            const item = feeItems.find(i => i.id === id);
            if (!item) return;
            item.name = document.getElementById('editItemName').value;
            item.amount = parseFloat(document.getElementById('editItemAmount').value);
            item.jalur = document.getElementById('editItemJalur').value;
            item.active = document.getElementById('editItemStatus').checked;
            renderItems(); closeEditModal();
            showToast(`Item "${item.name}" diperbarui`, 'success');
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            let icon = type === 'success' ? 'fa-circle-check' : (type === 'danger' ? 'fa-circle-xmark' : 'fa-circle-info');
            toast.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 3000);
        }

        document.addEventListener('DOMContentLoaded', renderItems);
    </script>
</body>
</html>
