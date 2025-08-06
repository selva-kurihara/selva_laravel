<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>会員登録完了</title>
</head>

<body>
    <h2>{{ $member->name_sei }} {{ $member->name_mei }} 様</h2>

    <p>この度は会員登録いただき、誠にありがとうございます。</p>

    <p>以下の内容で登録が完了しました：</p>

    <table>
        <tr>
            <th>氏名：</th>
            <td>{{ $member->name_sei }} {{ $member->name_mei }}</td>
        </tr>
        <tr>
            <th>ニックネーム：</th>
            <td>{{ $member->nickname }}</td>
        </tr>
        <tr>
            <th>メールアドレス：</th>
            <td>{{ $member->email }}</td>
        </tr>
    </table>

    <p>今後ともよろしくお願いいたします。</p>

    <hr>
    <p>※このメールは自動送信されています。</p>
</body>

</html>
