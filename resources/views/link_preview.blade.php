<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>URL-shortener</title>
    <link rel='stylesheet' href='{{ asset('css/styles.css') }}'>
</head>

<body>
    <div class="container">
        <h2 id="title">Куда ведёт эта ссылка?</h2>

        <h4>{{ $currentUrl }}</h4>

        <h1>&darr;</h1>

        <h4>{{ $link->original_url }}</h4>

        <h3>Создано: {{ $link->created_at }}</h3>

        <h3>Переходов: {{ $link->clicks_count }}</h3>

        <div class="navigation_buttons">
            <a href="{{ $currentUrl }}">[Перейти на сайт]</a>
            <a href="/">[Идти на главную]</a>
        </div>
    </div>
</body>

</html>