<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern POS System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #0ea5e9;
            --accent: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --darker: #0f172a;
            --light: #e2e8f0;
            --gray: #64748b;
        }

        body {
            background-color: var(--dark);
            color: var(--light);
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background-color: var(--darker);
            height: 100vh;
            position: fixed;
            width: 80px;
            transition: width 0.3s;
            overflow: hidden;
            z-index: 40;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .sidebar:hover {
            width: 240px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: #94a3b8;
            transition: all 0.2s;
            white-space: nowrap;
            border-left: 3px solid transparent;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--light);
        }

        .sidebar-item.active {
            border-left-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.1);
            color: var(--primary);
        }

        .sidebar-icon {
            font-size: 1.25rem;
            width: 2rem;
        }

        .sidebar-text {
            margin-left: 1rem;
            opacity: 0.9;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .product-card {
            background-color: #2d3748;
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 2px solid transparent;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            border-color: rgba(79, 70, 229, 0.3);
        }

        .product-image {
            height: 120px;
            background-size: cover;
            background-position: center;
            background-color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 50%);
        }

        .product-icon {
            font-size: 3rem;
            z-index: 1;
        }

        .product-info {
            padding: 0.75rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .product-description {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .product-price {
            color: var(--secondary);
            font-weight: 600;
            margin-top: auto;
        }

        .category-scroll {
            display: flex;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
            scrollbar-color: var(--gray) var(--dark);
        }

        .category-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .category-scroll::-webkit-scrollbar-track {
            background: var(--dark);
            border-radius: 3px;
        }

        .category-scroll::-webkit-scrollbar-thumb {
            background-color: var(--gray);
            border-radius: 3px;
        }

        .category-pill {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-right: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
            background-color: #334155;
            color: #94a3b8;
        }

        .category-pill.active {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .category-pill:not(.active):hover {
            background-color: #475569;
            color: var(--light);
        }

        .search-container {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 0.75rem;
            background-color: #2d3748;
            border: 1px solid #4b5563;
            color: var(--light);
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }

        .search-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
        }

        .cart-container {
            background-color: var(--darker);
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 2rem);
            position: sticky;
            top: 1rem;
        }

        .cart-header {
            padding: 1.25rem;
            border-bottom: 1px solid #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .cart-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-body::-webkit-scrollbar {
            width: 6px;
        }

        .cart-body::-webkit-scrollbar-track {
            background: var(--darker);
            border-radius: 3px;
        }

        .cart-body::-webkit-scrollbar-thumb {
            background-color: var(--gray);
            border-radius: 3px;
        }

        .cart-footer {
            padding: 1.25rem;
            border-top: 1px solid #334155;
        }

        .cart-item {
            background-color: #2d3748;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
            transition: all 0.2s;
            border: 1px solid #4b5563;
        }

        .cart-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .cart-item-name {
            font-weight: 600;
            flex-grow: 1;
            line-height: 1.2;
        }

        .cart-item-price {
            color: var(--secondary);
            font-weight: 600;
            white-space: nowrap;
            margin-left: 1rem;
        }

        .cart-item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            background-color: var(--darker);
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #4b5563;
        }

        .quantity-btn {
            background-color: transparent;
            color: var(--light);
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
            user-select: none;
        }

        .quantity-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .quantity-input {
            width: 2.5rem;
            text-align: center;
            background-color: transparent;
            border: none;
            color: var(--light);
            font-weight: 600;
            padding: 0.25rem;
            font-size: 0.95rem;
        }

        .quantity-input:focus {
            outline: none;
        }

        .cart-item-note {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-top: 0.75rem;
            padding: 0.75rem;
            background-color: var(--darker);
            border-radius: 0.5rem;
            border-left: 3px solid var(--warning);
            line-height: 1.4;
        }

        .action-icon {
            cursor: pointer;
            transition: all 0.2s;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        .note-icon {
            color: var(--warning);
            background-color: rgba(245, 158, 11, 0.1);
        }

        .note-icon:hover {
            background-color: rgba(245, 158, 11, 0.2);
        }

        .remove-icon {
            color: var(--danger);
            background-color: rgba(239, 68, 68, 0.1);
        }

        .remove-icon:hover {
            background-color: rgba(239, 68, 68, 0.2);
        }

        .empty-cart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #94a3b8;
            text-align: center;
            padding: 2rem;
        }

        .empty-cart-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .summary-row.total {
            border-top: 1px solid #334155;
            padding-top: 0.75rem;
            margin-top: 0.75rem;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .summary-label {
            color: #94a3b8;
        }

        .summary-value {
            font-weight: 500;
        }

        .summary-value.total {
            color: var(--secondary);
        }

        .btn {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .btn-icon {
            margin-right: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-secondary {
            background-color: #475569;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #334155;
        }

        .btn-outline-primary {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }

        .btn-outline-danger {
            background-color: transparent;
            border: 1px solid var(--danger);
            color: var(--danger);
        }

        .btn-outline-danger:hover {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .btn-block {
            width: 100%;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background-color: var(--darker);
            margin: 5% auto;
            border-radius: 1rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s;
            border: 1px solid #334155;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            border-bottom: 1px solid #334155;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close:hover {
            color: var(--light);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding: 1.25rem;
            border-top: 1px solid #334155;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #94a3b8;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: #2d3748;
            border: 1px solid #4b5563;
            color: var(--light);
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }

        .form-input::placeholder {
            color: #6b7280;
        }

        .customer-info {
            background-color: #2d3748;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #4b5563;
        }

        .customer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .customer-name {
            font-weight: 600;
            font-size: 1.125rem;
        }

        .customer-details {
            font-size: 0.875rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            margin-top: 0.25rem;
        }

        .customer-details i {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-primary {
            background-color: rgba(79, 70, 229, 0.2);
            color: var(--primary);
        }

        .badge-success {
            background-color: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .badge-warning {
            background-color: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .badge-danger {
            background-color: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .payment-method {
            background-color: #2d3748;
            border: 2px solid transparent;
            border-radius: 0.75rem;
            padding: 1rem 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method:hover {
            background-color: #374151;
        }

        .payment-method.active {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.1);
        }

        .payment-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--light);
        }

        .payment-name {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .product-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            z-index: 2;
        }

        .product-badge.new {
            background-color: var(--primary);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .product-badge.discount {
            background-color: var(--danger);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }
    </style>
</head>

<body class="min-vh-100">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-item active">
            <div class="sidebar-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="sidebar-text">Penjualan</div>
        </div>
        <div class="sidebar-item">
            <div class="sidebar-icon"><i class="fas fa-box"></i></div>
            <div class="sidebar-text">Inventaris</div>
        </div>
        <div class="sidebar-item">
            <div class="sidebar-icon"><i class="fas fa-users"></i></div>
            <div class="sidebar-text">Pelanggan</div>
        </div>
        <div class="sidebar-item">
            <div class="sidebar-icon"><i class="fas fa-history"></i></div>
            <div class="sidebar-text">Riwayat</div>
        </div>
        <div class="sidebar-item">
            <div class="sidebar-icon"><i class="fas fa-chart-bar"></i></div>
            <div class="sidebar-text">Laporan</div>
        </div>
        <div class="sidebar-item">
            <div class="sidebar-icon"><i class="fas fa-cog"></i></div>
            <div class="sidebar-text">Pengaturan</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ms-5 ms-md-6 p-4">
        <div class="row g-4">
            <!-- Products Section -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold">Penjualan</h1>
                    <div class="d-flex gap-3">
                        <button class="btn btn-outline-primary" id="openCustomerModal">
                            <i class="fas fa-user btn-icon"></i>Pelanggan
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-history btn-icon"></i>Riwayat
                        </button>
                    </div>
                </div>

                <!-- Search and Categories -->
                <div class="mb-4">
                    <div class="search-container mb-3">
                        <input type="text" class="search-input form-control" placeholder="Cari produk...">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="category-scroll">
                        <div class="category-pill active">Semua</div>
                        <div class="category-pill">Makanan</div>
                        <div class="category-pill">Minuman</div>
                        <div class="category-pill">Snack</div>
                        <div class="category-pill">Dessert</div>
                        <div class="category-pill">Paket</div>
                        <div class="category-pill">Favorit</div>
                        <div class="category-pill">Promo</div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="product-grid">
                    <!-- Product 1 -->
                    <div class="product-card" data-id="1" data-name="Nasi Goreng Spesial" data-price="35000"
                        data-description="Nasi goreng dengan telur, ayam, dan sayuran segar">
                        <div class="product-image">
                            <div class="product-icon">🍚</div>
                            <div class="product-badge discount">-15%</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Nasi Goreng Spesial</div>
                            <div class="product-description">Nasi goreng dengan telur, ayam, dan sayuran segar</div>
                            <div class="product-price">Rp 35.000</div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="product-card" data-id="2" data-name="Mie Goreng Seafood" data-price="38000"
                        data-description="Mie goreng dengan udang, cumi, dan sayuran">
                        <div class="product-image">
                            <div class="product-icon">🍜</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Mie Goreng Seafood</div>
                            <div class="product-description">Mie goreng dengan udang, cumi, dan sayuran</div>
                            <div class="product-price">Rp 38.000</div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="product-card" data-id="3" data-name="Es Teh Manis" data-price="8000"
                        data-description="Teh manis dingin dengan es batu">
                        <div class="product-image">
                            <div class="product-icon">🧋</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Es Teh Manis</div>
                            <div class="product-description">Teh manis dingin dengan es batu</div>
                            <div class="product-price">Rp 8.000</div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="product-card" data-id="4" data-name="Jus Alpukat" data-price="18000"
                        data-description="Jus alpukat segar dengan susu dan gula">
                        <div class="product-image">
                            <div class="product-icon">🥑</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Jus Alpukat</div>
                            <div class="product-description">Jus alpukat segar dengan susu dan gula</div>
                            <div class="product-price">Rp 18.000</div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="product-card" data-id="5" data-name="Ayam Bakar" data-price="32000"
                        data-description="Ayam bakar bumbu kecap dengan sambal">
                        <div class="product-image">
                            <div class="product-icon">🍗</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Ayam Bakar</div>
                            <div class="product-description">Ayam bakar bumbu kecap dengan sambal</div>
                            <div class="product-price">Rp 32.000</div>
                        </div>
                    </div>

                    <!-- Product 6 -->
                    <div class="product-card" data-id="6" data-name="Sate Ayam" data-price="28000"
                        data-description="10 tusuk sate ayam dengan bumbu kacang">
                        <div class="product-image">
                            <div class="product-icon">🍢</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Sate Ayam</div>
                            <div class="product-description">10 tusuk sate ayam dengan bumbu kacang</div>
                            <div class="product-price">Rp 28.000</div>
                        </div>
                    </div>

                    <!-- Product 7 -->
                    <div class="product-card" data-id="7" data-name="Bakso Spesial" data-price="25000"
                        data-description="Bakso daging sapi dengan mie dan tahu">
                        <div class="product-image">
                            <div class="product-icon">🍲</div>
                            <div class="product-badge new">Baru</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Bakso Spesial</div>
                            <div class="product-description">Bakso daging sapi dengan mie dan tahu</div>
                            <div class="product-price">Rp 25.000</div>
                        </div>
                    </div>

                    <!-- Product 8 -->
                    <div class="product-card" data-id="8" data-name="Es Krim Sundae" data-price="15000"
                        data-description="Es krim vanilla dengan sirup coklat dan kacang">
                        <div class="product-image">
                            <div class="product-icon">🍦</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Es Krim Sundae</div>
                            <div class="product-description">Es krim vanilla dengan sirup coklat dan kacang</div>
                            <div class="product-price">Rp 15.000</div>
                        </div>
                    </div>

                    <!-- Product 9 -->
                    <div class="product-card" data-id="9" data-name="Nasi Goreng Kambing" data-price="42000"
                        data-description="Nasi goreng dengan daging kambing dan rempah">
                        <div class="product-image">
                            <div class="product-icon">🍛</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Nasi Goreng Kambing</div>
                            <div class="product-description">Nasi goreng dengan daging kambing dan rempah</div>
                            <div class="product-price">Rp 42.000</div>
                        </div>
                    </div>

                    <!-- Product 10 -->
                    <div class="product-card" data-id="10" data-name="Capcay Seafood" data-price="30000"
                        data-description="Tumis sayuran dengan udang dan cumi">
                        <div class="product-image">
                            <div class="product-icon">🥘</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Capcay Seafood</div>
                            <div class="product-description">Tumis sayuran dengan udang dan cumi</div>
                            <div class="product-price">Rp 30.000</div>
                        </div>
                    </div>

                    <!-- Product 11 -->
                    <div class="product-card" data-id="11" data-name="Kopi Susu" data-price="12000"
                        data-description="Kopi dengan susu dan gula aren">
                        <div class="product-image">
                            <div class="product-icon">☕</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Kopi Susu</div>
                            <div class="product-description">Kopi dengan susu dan gula aren</div>
                            <div class="product-price">Rp 12.000</div>
                        </div>
                    </div>

                    <!-- Product 12 -->
                    <div class="product-card" data-id="12" data-name="Pudding Coklat" data-price="10000"
                        data-description="Pudding coklat dengan saus vanilla">
                        <div class="product-image">
                            <div class="product-icon">🍮</div>
                        </div>
                        <div class="product-info">
                            <div class="product-name">Pudding Coklat</div>
                            <div class="product-description">Pudding coklat dengan saus vanilla</div>
                            <div class="product-price">Rp 10.000</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Section -->
            <div class="col-lg-4">
                <div class="cart-container">
                    <div class="cart-header">
                        <div class="cart-title">Keranjang Belanja</div>
                        <button class="btn btn-outline-danger" id="clearCart">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <!-- Customer Info (if selected) -->
                    <div id="customerInfo" class="mx-4 mt-4" style="display: none;">
                        <div class="customer-info">
                            <div class="customer-header">
                                <div class="customer-name" id="selectedCustomerName">Budi Santoso</div>
                                <button class="text-secondary hover-text-light" id="changeCustomer">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            <div class="customer-details" id="selectedCustomerPhone">
                                <i class="fas fa-phone-alt"></i> 081234567890
                            </div>
                            <div class="customer-details" id="selectedCustomerAddress">
                                <i class="fas fa-map-marker-alt"></i> Jl. Merdeka No. 123, Jakarta
                            </div>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div class="cart-body">
                        <!-- Empty Cart Message -->
                        <div id="emptyCart" class="empty-cart">
                            <i class="fas fa-shopping-cart empty-cart-icon"></i>
                            <div class="fs-5 fw-semibold mb-2">Keranjang Kosong</div>
                            <p class="fs-6">Pilih produk untuk memulai pesanan</p>
                        </div>

                        <!-- Cart Items Container -->
                        <div id="cartItems" style="display: none;">
                            <!-- Cart items will be added here dynamically -->
                        </div>
                    </div>

                    <!-- Cart Footer -->
                    <div class="cart-footer">
                        <!-- Cart Summary -->
                        <div id="cartSummary" style="display: none;">
                            <div class="summary-row">
                                <div class="summary-label">Subtotal</div>
                                <div class="summary-value" id="subtotal">Rp 0</div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-label">Pajak (10%)</div>
                                <div class="summary-value" id="tax">Rp 0</div>
                            </div>
                            <div class="summary-row total">
                                <div class="summary-label">Total</div>
                                <div class="summary-value total" id="total">Rp 0</div>
                            </div>
                        </div>

                        <!-- Cart Actions -->
                        <div class="mt-3">
                            <button class="btn btn-primary w-100" id="checkoutBtn">
                                <i class="fas fa-cash-register btn-icon"></i>Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal" id="customerModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Informasi Pelanggan</div>
                <button class="modal-close btn-close close-modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" id="customerName" class="form-input form-control"
                        placeholder="Masukkan nama pelanggan">
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" id="customerPhone" class="form-input form-control"
                        placeholder="Masukkan nomor telepon">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea id="customerAddress" class="form-input form-control" rows="2"
                        placeholder="Masukkan alamat (opsional)"></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea id="customerNotes" class="form-input form-control" rows="2"
                        placeholder="Masukkan catatan tambahan (opsional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Batal</button>
                <button class="btn btn-primary" id="saveCustomer">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Item Note Modal -->
    <div class="modal" id="noteModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Tambah Catatan</div>
                <button class="modal-close btn-close close-modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <label class="form-label">Catatan untuk <span id="noteItemName"
                            class="font-semibold text-white"></span></label>
                    <textarea id="itemNote" class="form-input form-control" rows="3"
                        placeholder="Contoh: Tidak pedas, tanpa es, dll."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Batal</button>
                <button class="btn btn-primary" id="saveNote">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div class="modal" id="checkoutModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Pembayaran</div>
                <button class="modal-close btn-close close-modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label">Metode Pembayaran</label>
                    <div class="payment-methods">
                        <div class="payment-method active" data-method="cash">
                            <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="payment-name">Tunai</div>
                        </div>
                        <div class="payment-method" data-method="card">
                            <div class="payment-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="payment-name">Kartu</div>
                        </div>
                        <div class="payment-method" data-method="qris">
                            <div class="payment-icon"><i class="fas fa-qrcode"></i></div>
                            <div class="payment-name">QRIS</div>
                        </div>
                    </div>
                </div>

                <div id="cashPaymentForm">
                    <div class="form-group">
                        <label class="form-label">Total Pembayaran</label>
                        <div class="fs-4 fw-bold text-success mb-2" id="checkoutTotal">Rp 0</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Diterima</label>
                        <input type="number" id="amountReceived" class="form-input form-control" placeholder="0">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Kembalian</label>
                        <div class="fs-4 fw-bold" id="changeAmount">Rp 0</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Batal</button>
                <button class="btn btn-success" id="completePayment">
                    <i class="fas fa-check btn-icon"></i>Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS (with Popper.js for modals) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>

    <script>
        // Global variables
        let cart = [];
        let currentCustomer = null;
        let currentNoteItemId = null;

        // DOM Elements
        const productCards = document.querySelectorAll('.product-card');
        const cartItemsContainer = document.getElementById('cartItems');
        const emptyCartMessage = document.getElementById('emptyCart');
        const cartSummaryContainer = document.getElementById('cartSummary');
        const subtotalElement = document.getElementById('subtotal');
        const taxElement = document.getElementById('tax');
        const totalElement = document.getElementById('total');
        const clearCartButton = document.getElementById('clearCart');
        const checkoutButton = document.getElementById('checkoutBtn');
        const categoryPills = document.querySelectorAll('.category-pill');
        const searchInput = document.querySelector('.search-input');

        // Customer Modal Elements
        const customerModal = document.getElementById('customerModal');
        const openCustomerModalBtn = document.getElementById('openCustomerModal');
        const saveCustomerBtn = document.getElementById('saveCustomer');
        const customerInfoContainer = document.getElementById('customerInfo');
        const selectedCustomerName = document.getElementById('selectedCustomerName');
        const selectedCustomerPhone = document.getElementById('selectedCustomerPhone');
        const selectedCustomerAddress = document.getElementById('selectedCustomerAddress');
        const changeCustomerBtn = document.getElementById('changeCustomer');

        // Note Modal Elements
        const noteModal = document.getElementById('noteModal');
        const noteItemNameElement = document.getElementById('noteItemName');
        const itemNoteTextarea = document.getElementById('itemNote');
        const saveNoteBtn = document.getElementById('saveNote');

        // Checkout Modal Elements
        const checkoutModal = document.getElementById('checkoutModal');
        const checkoutTotalElement = document.getElementById('checkoutTotal');
        const amountReceivedInput = document.getElementById('amountReceived');
        const changeAmountElement = document.getElementById('changeAmount');
        const completePaymentBtn = document.getElementById('completePayment');
        const paymentMethods = document.querySelectorAll('.payment-method');

        // Format currency
        function formatCurrency(amount) {
            return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Add item to cart
        function addToCart(id, name, price, description) {
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.total = existingItem.quantity * existingItem.price;
                updateCartUI();
            } else {
                const newItem = {
                    id: id,
                    name: name,
                    price: price,
                    description: description,
                    quantity: 1,
                    total: price,
                    note: ''
                };
                cart.push(newItem);
                updateCartUI();
            }

            // Show animation effect
            const cartContainer = document.querySelector('.cart-container');
            cartContainer.style.animation = 'none';
            setTimeout(() => {
                cartContainer.style.animation = 'pulse 0.3s';
            }, 10);
        }

        // Update cart quantity
        function updateQuantity(id, newQuantity) {
            const item = cart.find(item => item.id === id);
            if (item) {
                if (newQuantity <= 0) {
                    removeFromCart(id);
                } else {
                    item.quantity = newQuantity;
                    item.total = item.quantity * item.price;
                    updateCartUI();
                }
            }
        }

        // Remove item from cart
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartUI();
        }

        // Clear cart
        function clearCart() {
            cart = [];
            updateCartUI();
        }

        // Add note to cart item
        function addNoteToItem(id, note) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.note = note;
                updateCartUI();
            }
        }

        // Calculate cart totals
        function calculateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
            const tax = Math.round(subtotal * 0.1);
            const total = subtotal + tax;

            return {
                subtotal,
                tax,
                total
            };
        }

        // Update cart UI
        function updateCartUI() {
            if (cart.length === 0) {
                emptyCartMessage.style.display = 'flex';
                cartItemsContainer.style.display = 'none';
                cartSummaryContainer.style.display = 'none';
                return;
            }

            emptyCartMessage.style.display = 'none';
            cartItemsContainer.style.display = 'block';
            cartSummaryContainer.style.display = 'block';

            // Clear cart items
            cartItemsContainer.innerHTML = '';

            // Add cart items
            cart.forEach(item => {
                const cartItemElement = document.createElement('div');
                cartItemElement.className = 'cart-item';

                let cartItemHTML = `
                    <div class="cart-item-header">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${formatCurrency(item.total)}</div>
                    </div>
                `;

                if (item.description) {
                    cartItemHTML += `
                        <div class="text-xs text-gray-400 mb-2">${item.description}</div>
                    `;
                }

                cartItemHTML += `
                    <div class="cart-item-actions">
                        <div class="quantity-control">
                            <div class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</div>
                            <input type="number" class="quantity-input" value="${item.quantity}" 
                                onchange="updateQuantity(${item.id}, parseInt(this.value) || 1)" min="1">
                            <div class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="action-icon note-icon" onclick="openNoteModal(${item.id}, '${item.name}', '${item.note.replace(/'/g, "\\'")}')">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <div class="action-icon remove-icon" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
                    </div>
                `;

                if (item.note) {
                    cartItemHTML += `
                        <div class="cart-item-note">
                            <i class="fas fa-quote-left mr-1 text-xs"></i> ${item.note}
                        </div>
                    `;
                }

                cartItemElement.innerHTML = cartItemHTML;
                cartItemsContainer.appendChild(cartItemElement);
            });

            // Update totals
            const {
                subtotal,
                tax,
                total
            } = calculateTotals();
            subtotalElement.textContent = formatCurrency(subtotal);
            taxElement.textContent = formatCurrency(tax);
            totalElement.textContent = formatCurrency(total);

            // Update checkout modal total
            checkoutTotalElement.textContent = formatCurrency(total);
        }

        // Open note modal
        function openNoteModal(id, name, note) {
            currentNoteItemId = id;
            noteItemNameElement.textContent = name;
            itemNoteTextarea.value = note;
            noteModal.style.display = 'block';
        }

        // Filter products
        function filterProducts(category, searchTerm = '') {
            productCards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const description = card.dataset.description.toLowerCase();
                const matchesSearch = searchTerm === '' ||
                    name.includes(searchTerm) ||
                    description.includes(searchTerm);

                const matchesCategory = category === 'Semua' ||
                    (category === 'Makanan' && (name.includes('nasi') || name.includes('mie') || name.includes(
                        'ayam') || name.includes('bakso') || name.includes('sate') || name.includes(
                        'capcay'))) ||
                    (category === 'Minuman' && (name.includes('teh') || name.includes('jus') || name.includes(
                        'kopi'))) ||
                    (category === 'Dessert' && (name.includes('es krim') || name.includes('pudding')));

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Event Listeners

        // Product cards
        productCards.forEach(card => {
            card.addEventListener('click', () => {
                const id = parseInt(card.dataset.id);
                const name = card.dataset.name;
                const price = parseInt(card.dataset.price);
                const description = card.dataset.description;
                addToCart(id, name, price, description);
            });
        });

        // Clear cart button
        clearCartButton.addEventListener('click', () => {
            if (cart.length > 0 && confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                clearCart();
            }
        });

        // Checkout button
        checkoutButton.addEventListener('click', () => {
            if (cart.length === 0) {
                alert('Keranjang masih kosong. Silakan tambahkan produk terlebih dahulu.');
                return;
            }

            const {
                total
            } = calculateTotals();
            checkoutTotalElement.textContent = formatCurrency(total);
            amountReceivedInput.value = '';
            changeAmountElement.textContent = 'Rp 0';
            checkoutModal.style.display = 'block';
        });

        // Amount received input
        amountReceivedInput.addEventListener('input', () => {
            const {
                total
            } = calculateTotals();
            const received = parseInt(amountReceivedInput.value) || 0;
            const change = received - total;

            if (change >= 0) {
                changeAmountElement.textContent = formatCurrency(change);
                changeAmountElement.classList.remove('text-danger');
                changeAmountElement.classList.add('text-success');
            } else {
                changeAmountElement.textContent = formatCurrency(change);
                changeAmountElement.classList.remove('text-success');
                changeAmountElement.classList.add('text-danger');
            }
        });

        // Complete payment button
        completePaymentBtn.addEventListener('click', () => {
            const {
                total
            } = calculateTotals();
            const received = parseInt(amountReceivedInput.value) || 0;
            const activeMethod = document.querySelector('.payment-method.active').dataset.method;

            if (activeMethod === 'cash' && received < total) {
                alert('Jumlah yang diterima kurang dari total pembayaran.');
                return;
            }

            alert('Pembayaran berhasil! Terima kasih atas pesanan Anda.');
            clearCart();
            checkoutModal.style.display = 'none';
        });

        // Payment methods
        paymentMethods.forEach(method => {
            method.addEventListener('click', () => {
                document.querySelector('.payment-method.active').classList.remove('active');
                method.classList.add('active');

                const selectedMethod = method.dataset.method;
                const cashForm = document.getElementById('cashPaymentForm');

                if (selectedMethod === 'cash') {
                    cashForm.style.display = 'block';
                } else {
                    cashForm.style.display = 'none';
                }
            });
        });

        // Category pills
        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                document.querySelector('.category-pill.active').classList.remove('active');
                pill.classList.add('active');

                const category = pill.textContent;
                const searchTerm = searchInput.value.toLowerCase();
                filterProducts(category, searchTerm);
            });
        });

        // Search input
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const activeCategory = document.querySelector('.category-pill.active').textContent;
            filterProducts(activeCategory, searchTerm);
        });

        // Customer modal
        openCustomerModalBtn.addEventListener('click', () => {
            customerModal.style.display = 'block';

            if (currentCustomer) {
                document.getElementById('customerName').value = currentCustomer.name;
                document.getElementById('customerPhone').value = currentCustomer.phone;
                document.getElementById('customerAddress').value = currentCustomer.address;
                document.getElementById('customerNotes').value = currentCustomer.notes || '';
            } else {
                document.getElementById('customerName').value = '';
                document.getElementById('customerPhone').value = '';
                document.getElementById('customerAddress').value = '';
                document.getElementById('customerNotes').value = '';
            }
        });

        changeCustomerBtn.addEventListener('click', () => {
            customerModal.style.display = 'block';
        });

        saveCustomerBtn.addEventListener('click', () => {
            const name = document.getElementById('customerName').value.trim();
            const phone = document.getElementById('customerPhone').value.trim();
            const address = document.getElementById('customerAddress').value.trim();
            const notes = document.getElementById('customerNotes').value.trim();

            if (!name) {
                alert('Nama pelanggan tidak boleh kosong.');
                return;
            }

            currentCustomer = {
                name,
                phone,
                address,
                notes
            };

            selectedCustomerName.textContent = name;
            selectedCustomerPhone.innerHTML = phone ? `<i class="fas fa-phone-alt"></i> ${phone}` : '';
            selectedCustomerAddress.innerHTML = address ? `<i class="fas fa-map-marker-alt"></i> ${address}` : '';

            customerInfoContainer.style.display = 'block';
            customerModal.style.display = 'none';
        });

        // Note modal
        saveNoteBtn.addEventListener('click', () => {
            const note = itemNoteTextarea.value.trim();
            addNoteToItem(currentNoteItemId, note);
            noteModal.style.display = 'none';
        });

        // Close modals
        const closeModalBtns = document.querySelectorAll('.close-modal');
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                customerModal.style.display = 'none';
                noteModal.style.display = 'none';
                checkoutModal.style.display = 'none';
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target === customerModal) {
                customerModal.style.display = 'none';
            }
            if (event.target === noteModal) {
                noteModal.style.display = 'none';
            }
            if (event.target === checkoutModal) {
                checkoutModal.style.display = 'none';
            }
        });

        // Initialize UI
        updateCartUI();
    </script>
</body>

</html>
