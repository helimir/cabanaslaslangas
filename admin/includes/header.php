<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cabañas Las Lengas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos personalizados del admin -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    <style>
        /* Estilos de respaldo en caso de que no cargue el CSS */
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: #1a1a2e;
            color: #fff;
            transition: all 0.3s ease;
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #sidebar.collapsed {
            width: 70px;
        }
        #sidebar.collapsed .nav-text {
            display: none;
        }
        #sidebar.collapsed .sidebar-header h4 {
            font-size: 1.1rem;
        }
        #sidebar.collapsed .sidebar-header small {
            display: none;
        }
        #sidebar.collapsed .nav-link {
            text-align: center;
            padding: 10px 5px;
            justify-content: center;
        }
        #sidebar.collapsed .nav-link i {
            font-size: 1.4rem;
            margin: 0;
        }
        #sidebar.collapsed .nav-link .badge {
            display: none;
        }
        #main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
            background: #f0f2f5;
        }
        #main-content.expanded {
            margin-left: 70px;
        }
        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .sidebar-header h4 {
            font-size: 1.4rem;
            margin: 0;
            white-space: nowrap;
        }
        .sidebar-header small {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
        }
        #sidebar .nav {
            padding: 10px 8px;
        }
        #sidebar .nav-item {
            margin-bottom: 2px;
        }
        #sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            border-radius: 8px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-size: 0.9rem;
        }
        #sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.08);
            color: #fff;
        }
        #sidebar .nav-link.active {
            background-color: rgba(248,193,70,0.2);
            color: #f8c146;
        }
        #sidebar .nav-link i {
            font-size: 1.2rem;
            width: 28px;
            text-align: center;
            flex-shrink: 0;
        }
        #sidebar .nav-link .nav-text {
            margin-left: 5px;
        }
        #sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 0.7rem;
        }
        #toggle-sidebar {
            background: transparent;
            border: none;
            color: #333;
            font-size: 1.3rem;
            padding: 5px 10px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        #toggle-sidebar:hover {
            background: rgba(0,0,0,0.05);
        }
        .img-thumbnail-sm {
            width: 50px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table th {
            white-space: nowrap;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        .table td {
            vertical-align: middle;
            white-space: nowrap;
        }
        .table td:last-child {
            white-space: normal;
        }
        @media (max-width: 992px) {
            #sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0;
            }
            #main-content.expanded {
                margin-left: 0;
            }
            #toggle-sidebar {
                display: inline-block !important;
            }
        }
        @media (min-width: 993px) {
            #toggle-sidebar {
                display: inline-block !important;
            }
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .btn-primary {
            background: #f8c146;
            border: none;
            color: #1a1a2e;
        }
        .btn-primary:hover {
            background: #e6b03a;
            color: #1a1a2e;
        }
        /* Scrollbar */
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body>