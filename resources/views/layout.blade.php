<!doctype html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? '傳銷帳務系統' }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .container { max-width: 980px; margin: 0 auto; }
        input, select, button { padding: .5rem; margin: .2rem 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: .5rem; text-align: left; }
        .card { border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 6px; }
        .row { display: flex; gap: 1rem; flex-wrap: wrap; }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>
