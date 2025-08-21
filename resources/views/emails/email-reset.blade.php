<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールアドレス変更認証</title>
</head>
<body>
    <p>{{ $member->name }} 様</p>

    <p>このたびはメールアドレスの変更をリクエストいただきありがとうございます。</p>

    <p>下記の認証コードを入力して、メールアドレスの変更を完了してください。</p>

    <h2 style="letter-spacing: 4px;">{{ $authCode }}</h2>

    <p>※このコードの有効期限は <strong>10分間</strong> です。<br>
       有効期限を過ぎた場合は、再度メールアドレス変更を行ってください。</p>

    <p>心当たりのない場合は、このメールを破棄してください。</p>

    <br>
    <p>-------------------------------------<br>
       {{ config('app.name') }} サポートチーム<br>
       {{ config('app.url') }}<br>
    </p>
</body>
</html>
