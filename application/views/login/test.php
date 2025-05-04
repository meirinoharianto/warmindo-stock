<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>POS Customer Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f1f2f3;
        }

        .sidebar {
            background: #2d353c;
            color: #fff;
            min-height: 100vh;
        }

        .sidebar a {
            color: #fff;
            display: block;
            padding: 10px 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #1a1e22;
        }

        .product {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .product img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .topbar {
            background: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .cart {
            background: #fff;
            padding: 15px;
            border-left: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <h4 class="mt-3 text-center">Menu</h4>
                <a href="#">Dashboard</a>
                <a href="#">Orders</a>
                <a href="#">Products</a>
                <a href="#">Settings</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-7 px-0">
                <div class="topbar">
                    <h5>POS Customer Order</h5>
                </div>
                <div class="p-3">
                    <div class="row">
                        <!-- Product Cards -->
                        <div class="col-md-4">
                            <div class="product">
                                <img src="https://via.placeholder.com/200x150" alt="Product">
                                <h6 class="mt-2">Product Name</h6>
                                <p>$10.00</p>
                                <button class="btn btn-sm btn-primary">Add</button>
                            </div>
                        </div>
                        <!-- Copy product card as needed -->
                    </div>
                </div>
            </div>

            <!-- Cart / Order Sidebar -->
            <div class="col-md-3 cart">
                <h5>Order Summary</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Product A
                        <span>$10.00</span>
                    </li>
                    <!-- More items -->
                </ul>
                <h6>Total: $10.00</h6>
                <button class="btn btn-success btn-block">Checkout</button>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>