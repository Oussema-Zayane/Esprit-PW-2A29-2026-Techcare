<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Back Office</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, #0A1F44 0%, #0d2a5a 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar-header h2 i {
            color: #00A86B;
        }

        .sidebar-header p {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 5px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            margin: 5px 0;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #00A86B;
        }

        .nav-item.active {
            background: rgba(0,168,107,0.15);
            color: #00A86B;
            border-left-color: #00A86B;
        }

        .nav-item i {
            width: 22px;
            font-size: 1.1rem;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        /* TOPBAR */
        .topbar {
            background: white;
            border-radius: 12px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .page-title h1 {
            font-size: 1.5rem;
            color: #0A1F44;
        }

        .page-title p {
            color: #666;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
        }

        .user-info span {
            color: #333;
            font-weight: 500;
        }

        .user-menu {
            position: relative;
            display: inline-block;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #00A86B, #007a4e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-avatar:hover {
            box-shadow: 0 4px 12px rgba(0, 168, 107, 0.3);
        }

        .user-avatar.active {
            box-shadow: 0 4px 12px rgba(0, 168, 107, 0.3);
        }

        .dropdown-menu {
            position: absolute;
            top: 55px;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            flex-direction: column;
            min-width: 180px;
            z-index: 1001;
        }

        .dropdown-menu.show {
            display: flex;
        }

        .dropdown-menu a {
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover {
            background: #f8f9fa;
            color: #00A86B;
            padding-left: 25px;
        }

        .dropdown-menu a.logout {
            color: #e74c3c;
        }

        .dropdown-menu a.logout:hover {
            background: #fff5f5;
            color: #c0392b;
        }

        /* CARTES STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-info h3 {
            font-size: 1.8rem;
            color: #0A1F44;
        }

        .stat-info p {
            color: #666;
            font-size: 0.85rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(0,168,107,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #00A86B;
        }

        /* TABLEAU */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-add {
            background: #00A86B;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-add:hover {
            background: #008f5a;
            transform: translateY(-2px);
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f5f5;
            padding: 8px 15px;
            border-radius: 25px;
        }

        .search-box input {
            border: none;
            background: none;
            outline: none;
            width: 250px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 20px;
            background: #f8f9fa;
            color: #555;
            font-weight: 600;
            font-size: 0.85rem;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-actif {
            background: #d4edda;
            color: #155724;
        }

        .badge-expiré {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-suspendu {
            background: #fff3cd;
            color: #856404;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn-view, .btn-edit, .btn-delete {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .btn-view {
            background: #3498db;
            color: white;
        }

        .btn-edit {
            background: #f39c12;
            color: white;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        .btn-view:active, .btn-edit:active, .btn-delete:active {
            color: #000000 !important;
            transform: scale(0.96);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
<?php
// Menu utilisateur
$currentUser = $_SESSION['username'] ?? 'Utilisateur';
?>