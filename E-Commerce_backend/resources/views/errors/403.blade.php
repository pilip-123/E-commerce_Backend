<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #f0fdf4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .error-card {
            max-width: 480px;
            width: 100%;
            border-radius: 28px;
            border: none;
            box-shadow: 0 20px 60px -12px rgba(0,0,0,0.10);
            background: #fff;
            padding: 3rem 2.5rem;
            text-align: center;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fef2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-box i {
            font-size: 2.2rem;
            color: #dc2626;
        }
        h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        p {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.25rem;
            line-height: 1.6;
        }
        .badge-perm {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            margin: 1rem 0 1.5rem;
            letter-spacing: 0.02em;
        }
        .btn-back {
            background: #059669;
            color: #fff;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.15s;
        }
        .btn-back:hover {
            background: #047857;
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-box">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <h2>Access Denied</h2>
        <p>You don't have permission to access this page.</p>
        <p style="font-size:0.8rem; color:#94a3b8;">This action has been restricted for your role.</p>
        @if (isset($exception) && method_exists($exception, 'getMessage') && $exception->getMessage())
            <div class="badge-perm">{{ $exception->getMessage() }}</div>
        @endif
        <div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.dashboard') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>
