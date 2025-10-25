<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Universitario')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        .login-header {
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border: none;
        }
        .logo-uagrm {
            display: inline-block;
            font-family: 'Arial Black', Arial, sans-serif;
            font-weight: 900;
            text-align: center;
            line-height: 0.8;
        }
        .logo-top {
            font-size: 2.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .logo-bottom {
            font-size: 2.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .btn-uagrm {
            background: linear-gradient(135deg, #dc3545 0%, #0d6efd 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }
        .btn-uagrm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
            color: white;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .form-label {
            color: #495057;
            margin-bottom: 8px;
        }
        .university-logo {
            animation: fadeInDown 1s ease-out;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login-card {
            animation: fadeInUp 0.8s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>