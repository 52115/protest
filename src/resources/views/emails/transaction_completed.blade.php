<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>取引が完了しました</title>
</head>
<body>
    <h2>取引が完了しました</h2>
    <p>以下の商品の取引が完了しました。</p>
    <p>商品名: {{ $item->name }}</p>
    <p>商品価格: ¥{{ number_format($item->price) }}</p>
    <p>購入者: {{ $buyer->name }}</p>
    <p>取引チャット画面でお客様の評価をお待ちしています。</p>
</body>
</html>

