<!DOCTYPE html>

<html>

<head>
    <title>Laravel Shop</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f1f3f6;
            color: #333;
        }

        /* ================= NAVBAR ================= */

        .nav {
            background: #1f2937;
            padding: 15px 30px;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 600;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        /* ================= CONTAINER ================= */

        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }

        h2 {
            margin-bottom: 20px;
        }

        /* ================= BUTTONS ================= */

        .btn {
            padding: 7px 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-secondary {
            background: #6b7280;
            color: #fff;
        }

        .btn-light {
            background: #e5e7eb;
            color: #000;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px; }
        .variant-grid { display: grid; grid-template-columns: 1fr 1fr 120px; gap: 8px; align-items: center; }
        .check-row { display: inline-flex; gap: 8px; align-items: center; margin: 8px 18px 8px 0; }
        .check-row input { width: auto; }
        .search-suggestions { position: absolute; z-index: 5; width: 100%; background: #fff; border: 1px solid #d1d5db; border-radius: 6px; box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        .search-suggestions a { display: block; padding: 9px 12px; color: #111827; text-decoration: none; }
        .search-suggestions a:hover { background: #f3f4f6; }
        .search-box { position: relative; }
        .sale-price { color: #059669; font-weight: 800; }
        .old-price { color: #9ca3af; text-decoration: line-through; margin-left: 8px; font-size: .9em; }
        .stock-ok { color: #166534; font-weight: 600; }
        .stock-out { color: #991b1b; font-weight: 600; }
        .toast-message { position: fixed; right: 20px; top: 20px; z-index: 20; min-width: 240px; box-shadow: 0 10px 25px rgba(0,0,0,.18); }
        .dark-mode { background: #111827; color: #e5e7eb; }
        .dark-mode .container, .dark-mode .product-card.modern, .dark-mode .product-detail-card { background: #1f2937; color: #e5e7eb; }
        .dark-mode input, .dark-mode select, .dark-mode textarea, .dark-mode .search-suggestions { background: #374151; color: #fff; border-color: #6b7280; }
        .dark-mode h1, .dark-mode h2, .dark-mode h3, .dark-mode h4 { color: #fff; }

        /* ================= TABLE (ADMIN) ================= */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
        }

        table th {
            background: #f9fafb;
            font-weight: 600;
        }

        table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        /* ================= FORM ================= */

        .form-group {
            margin-bottom: 15px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 9px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        .form-actions {
            margin-top: 20px;
        }

        /* ================= IMAGE PREVIEW ================= */

        .image-preview-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .preview-box {
            width: 140px;
            height: 140px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
        }

        .preview-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        .preview-text {
            font-size: 13px;
            color: #6b7280;
        }

        /* ================= FRONTEND PRODUCTS ================= */

        .frontend-products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .frontend-products-header h2 {
            margin: 0 0 5px;
        }

        .frontend-subtitle {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* ================= FRONTEND FILTER ================= */

        .frontend-filter {
            display: grid;
            grid-template-columns:
                minmax(180px, 1.5fr) minmax(150px, 1fr) minmax(150px, 1fr) minmax(130px, 0.8fr) auto;

            gap: 15px;

            padding: 20px;

            margin-bottom: 20px;

            background: #f8f9fa;

            border: 1px solid #e5e7eb;

            border-radius: 12px;
        }

        .frontend-filter .filter-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .frontend-filter .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .frontend-filter .filter-group input,
        .frontend-filter .filter-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #fff;
            font-size: 14px;
            outline: none;
        }

        .frontend-filter .filter-group input:focus,
        .frontend-filter .filter-group select:focus {
            border-color: #2563eb;
        }

        .frontend-filter .filter-buttons {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .frontend-filter .filter-buttons .btn {
            white-space: nowrap;
        }

        /* ================= FRONTEND RESULT INFO ================= */

        .product-result-info {
            padding: 12px 15px;
            margin-bottom: 20px;
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            color: #475569;
            font-size: 14px;
        }

        /* ================= FRONTEND PRODUCT GRID ================= */

        .frontend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        /* ================= MODERN PRODUCT CARD ================= */

        .product-card.modern {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .1);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .product-card.modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
        }

        .image-wrap {
            width: 100%;
            height: 200px;
            background: #f1f5f9;
        }

        .image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-image {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 14px;
        }

        .card-body {
            padding: 15px;
        }

        .product-title {
            margin: 0 0 6px;
            font-size: 17px;
            font-weight: 600;
        }

        .category {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .details {
            font-size: 14px;
            margin-bottom: 12px;
        }

        .card-footer {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .product-card-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }

        .product-card-actions > a,
        .product-card-actions > form {
            display: flex;
            min-width: 0;
        }

        .product-card-actions > a:first-child {
            grid-column: 1 / -1;
        }

        .product-card-actions .btn {
            width: 100%;
            min-height: 38px;
            white-space: nowrap;
        }

        .product-card-actions form {
            margin: 0 !important;
        }

        .price {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
        }

        /* ================= PRODUCT DETAIL PAGE ================= */

        .product-detail-wrapper {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: flex-start;
        }

        .detail-image-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
        }

        .detail-image-box img {
            width: 100%;
            height: 420px;
            object-fit: contain;
        }

        .detail-title {
            margin-top: 0;
            font-size: 26px;
            font-weight: 600;
        }

        .detail-category {
            color: #6b7280;
            margin: 10px 0;
        }

        .detail-price {
            font-size: 26px;
            font-weight: 700;
            color: #2563eb;
            margin: 15px 0;
        }

        .detail-description {
            margin-top: 20px;
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            line-height: 1.6;
        }

        .detail-actions {
            margin-top: 25px;
            display: flex;
            gap: 15px;
        }

        .price-label {
            font-size: 13px;
            color: #6b7280;
            margin-top: 15px;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ================= RELATED PRODUCTS ================= */

        .related-products-section {
            margin-top: 50px;
        }

        .related-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .related-header h3 {
            margin: 0;
        }

        .related-subtitle {
            margin: 5px 0 0;
            color: #777;
            font-size: 14px;
        }

        .related-result-info {
            margin-bottom: 20px;
            padding: 12px 15px;
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            color: #475569;
            font-size: 14px;
        }

        /* ================= RECENTLY VIEWED ================= */

        .recently-viewed-section {
            margin-top: 55px;
            padding-top: 35px;
            border-top: 1px solid #e5e7eb;
        }

        /* ================= RESPONSIVE FILTER ================= */

        @media (max-width: 1000px) {

            .frontend-filter {
                grid-template-columns:
                    repeat(2, minmax(180px, 1fr));
            }

            .frontend-filter .filter-buttons {
                align-items: center;
            }
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 768px) {

            .product-detail-grid {
                grid-template-columns: 1fr;
            }

            .detail-image-box img {
                height: 300px;
            }

            table thead {
                display: none;
            }

            table,
            table tbody,
            table tr,
            table td {
                display: block;
                width: 100%;
            }

            table tr {
                margin-bottom: 15px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 10px;
            }

            table td {
                border: none;
                padding: 8px 0;
            }
        }

        /* ================= HEADER ACTIONS ================= */

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }


        /* ================= ADMIN FILTER ================= */

        .admin-filter {
            display: grid;
            grid-template-columns:
                1.5fr 1fr 1fr 1fr 1fr auto auto;

            gap: 10px;

            padding: 20px;

            margin-bottom: 20px;

            background: #f8fafc;

            border: 1px solid #e5e7eb;

            border-radius: 10px;
        }


        /* ================= CATEGORY SEARCH ================= */

        .category-search {
            display: flex;

            gap: 10px;

            margin-bottom: 20px;
        }

        .category-search input {
            max-width: 400px;
        }


        /* ================= BULK BAR ================= */

        .bulk-bar {
            margin-bottom: 15px;
        }


        /* ================= STATUS ================= */

        .status {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 600;
        }

        .status.active {
            background: #dcfce7;

            color: #166534;
        }

        .status.inactive {
            background: #fee2e2;

            color: #991b1b;
        }


        /* ================= ALERT ================= */

        .alert {
            padding: 12px 15px;

            margin-bottom: 20px;

            border-radius: 7px;

            font-size: 14px;
        }

        .alert.success {
            background: #dcfce7;

            color: #166534;
        }

        .alert.error {
            background: #fee2e2;

            color: #991b1b;
        }


        /* ================= PAGINATION ================= */

        .pagination-wrapper {
            margin-top: 25px;

            display: flex;

            justify-content: center;
        }


        /* ================= FRONTEND FILTER ================= */

        .frontend-product-filter {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(150px, 1fr));

            gap: 15px;

            padding: 20px;

            margin-bottom: 25px;

            background: #f8fafc;

            border: 1px solid #e5e7eb;

            border-radius: 12px;
        }

        .frontend-product-filter .filter-group {
            display: flex;

            flex-direction: column;

            gap: 7px;
        }

        .frontend-product-filter label {
            font-size: 13px;

            font-weight: 600;

            color: #374151;
        }

        .frontend-product-filter input,
        .frontend-product-filter select {
            width: 100%;

            padding: 10px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            background: #fff;
        }

        .frontend-product-filter .filter-buttons {
            display: flex;

            align-items: flex-end;

            gap: 8px;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 1000px) {

            .admin-filter {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .frontend-product-filter {
                grid-template-columns:
                    repeat(2, 1fr);
            }
        }


        @media (max-width: 600px) {

            .header-actions {
                flex-direction: column;
            }

            .header-actions .btn {
                width: 100%;
            }

            .admin-filter {
                grid-template-columns: 1fr;
            }

            .category-search {
                flex-direction: column;
            }

            .category-search input {
                max-width: 100%;
            }

            .frontend-product-filter {
                grid-template-columns: 1fr;
            }

            .frontend-product-filter .filter-buttons {
                flex-direction: column;
            }

            .frontend-product-filter .filter-buttons .btn {
                width: 100%;
                text-align: center;
            }
        }

        /* ================= MOBILE ================= */

        @media (max-width: 600px) {

            .container {
                margin: 15px;
                padding: 18px;
            }

            .nav {
                padding: 15px 20px;
            }

            .nav a {
                display: inline-block;
                margin-bottom: 8px;
            }

            .frontend-filter {
                grid-template-columns: 1fr;
            }

            .frontend-filter .filter-buttons {
                align-items: stretch;
                flex-direction: column;
            }

            .frontend-filter .filter-buttons .btn {
                width: 100%;
                text-align: center;
            }

            .related-header {
                display: block;
            }

            .frontend-grid {
                grid-template-columns: 1fr;
            }

            .detail-actions {
                flex-direction: column;
            }

            .detail-actions .btn {
                width: 100%;
            }
        }
    </style>


</head>

<body>


    <!-- ================= NAVBAR ================= -->

    <div class="nav">

        <a href="{{ route('frontend.products') }}">
            Products
        </a>

        <a href="{{ route('wishlist') }}">
            ❤️ Wishlist
        </a>

        <a href="{{ route('compare') }}">Compare</a>

        <a href="{{ route('categories.index') }}">
            Categories
        </a>

        <a href="{{ route('product.index') }}">
            Admin Products
        </a>

        <a href="{{ route('product.trash') }}">
            🗑 Trash
        </a>

        <button type="button" class="btn btn-light" id="darkModeToggle">Dark mode</button>

    </div>

    <!-- ================= CONTENT ================= -->

    <div class="container">
        @yield('content')
    </div>

    <!-- ================= IMAGE PREVIEW SCRIPT ================= -->

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}">`;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle?.addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('shop-dark-mode', document.body.classList.contains('dark-mode') ? '1' : '0');
        });
        if (localStorage.getItem('shop-dark-mode') === '1') document.body.classList.add('dark-mode');

        document.querySelectorAll('.alert.success').forEach(function (alert) {
            alert.classList.add('toast-message');
            setTimeout(() => alert.remove(), 3500);
        });

        document.querySelectorAll('[data-live-search]').forEach(function (input) {
            const target = document.getElementById(input.dataset.liveSearch);
            let timer;
            input.addEventListener('input', function () {
                clearTimeout(timer);
                const term = input.value.trim();
                if (!term) { target.innerHTML = ''; return; }
                timer = setTimeout(() => fetch(`{{ route('frontend.products.search') }}?q=${encodeURIComponent(term)}`)
                    .then(response => response.json())
                    .then(items => target.innerHTML = items.map(item => `<a href="{{ url('/product-detail') }}/${item.id}">${item.name} <small>₹${item.discount_price || item.price}</small></a>`).join('')), 250);
            });
        });
    </script>


</body>

</html>