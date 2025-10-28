<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary" >
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">VAT Register</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto text-center mb-5">
                <h1 class="display-4 mb-3">VAT Purchase & Sale Register</h1>
                <p class="lead text-muted">Manage your inventory and VAT calculations efficiently</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text">Manage your product catalog with SKU, unit, and VAT rates.</p>
                        <a href="{{ url('/products') }}" class="btn btn-primary">Manage Products</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Purchases</h5>
                        <p class="card-text">Record and track all purchase transactions from suppliers.</p>
                        <a href="{{ url('/purchases') }}" class="btn btn-success">View Purchases</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Sales</h5>
                        <p class="card-text">Manage sales transactions with automatic VAT calculation.</p>
                        <a href="{{ url('/sales') }}" class="btn btn-info">View Sales</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-5 mb-5">
            <div class="col-md-8 mx-auto">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>Features</h4>
                        <ul class="list-unstyled mt-3">
                            <li>✓ Complete Product Management</li>
                            <li>✓ Purchase & Sales Tracking</li>
                            <li>✓ Automatic VAT Calculation (15%)</li>
                            <li>✓ Return Management with Stock Update</li>
                            <li>✓ Date Range Filtering & CSV Export</li>
                            <li>✓ Role-based Access Control</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2024 VAT Register System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>