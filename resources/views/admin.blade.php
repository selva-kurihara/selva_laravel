<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="/css/adminStyle.css">
</head>

<body>

  @hasSection('header')
    <div class="header">
        @yield('header')
    </div>
  @endif
  @yield('content')
</body>

</html>