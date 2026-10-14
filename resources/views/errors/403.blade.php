<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - CONAPDIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f4f6f9 0%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
        }
        .error-card {
            max-width: 520px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 48, 151, 0.15);
            padding: 3rem 2.5rem;
            text-align: center;
            border-top: 5px solid #ef172f;
        }
        .error-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-icon i {
            font-size: 3rem;
            color: #ef172f;
        }
        .error-code {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ef172f;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #001e5c;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-volver {
            background: #003097;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-volver:hover {
            background: #001e5c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 48, 151, 0.3);
        }
        .logo-conapdis {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 2rem;
            font-weight: 500;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <div class="error-code">Error 403</div>
        <h1 class="error-title">Acceso Denegado</h1>

        <p class="error-message">
            No tenés permisos suficientes para acceder a esta sección del sistema.
            <br>
            Si creés que esto es un error, contactá al administrador del sistema.
        </p>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" class="btn-volver">
            <i class="bi bi-arrow-left"></i>
            Volver al Panel
        </a>

        <div class="logo-conapdis">
            CONAPDIS &middot; Sistema de Gestión de Bienes
        </div>
    </div>
</body>
</html>