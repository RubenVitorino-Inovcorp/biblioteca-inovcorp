<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>A sua opinião foi aprovada!</h2>
    <p>Olá {{ $review->user->name }},</p>
    <p>A sua opinião sobre o livro <strong>{{ $review->book->title }}</strong> foi analisada e <strong>aprovada</strong> pela nossa equipa de moderação.</p>
    <p>Ela já se encontra visível para todos os utilizadores no nosso catálogo!</p>
    <br>
    <p>Obrigado por partilhar a sua opinião connosco!</p>
    <p>Com os melhores cumprimentos,<br>A equipa da InovCorp.</p>
</body>
</html>
