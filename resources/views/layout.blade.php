<!doctype html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? '傳銷帳務系統' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 1rem; }
        .container { max-width: 1080px; margin: 0 auto; }
        input, select, button, textarea { padding: .5rem; margin: .2rem 0; max-width: 100%; box-sizing: border-box; }
        textarea { min-height: 56px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; min-width: 780px; }
        th, td { border: 1px solid #ddd; padding: .5rem; text-align: left; vertical-align: top; }
        .card { border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; }
        .row { display: flex; gap: 1rem; flex-wrap: wrap; }
        @media (max-width: 768px) {
            body { margin: .6rem; }
            .row { gap: .5rem; }
            .card { padding: .75rem; }
            h1 { font-size: 1.2rem; }
            h3 { font-size: 1rem; }
            button { width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
