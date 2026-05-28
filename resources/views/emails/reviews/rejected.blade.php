<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Atualização sobre a sua opinião</h2>
    <p>Olá {{ $review->user->name }},</p>
    <p>A sua opinião sobre o livro <strong>{{ $review->book->title }}</strong> foi analisada pela nossa equipa de moderação.</p>
    <p>Infelizmente, a sua opinião foi <strong>recusada</strong> pelo seguinte motivo:</p>
    <blockquote style="border-left: 4px solid #ef4444; margin-left: 0; padding-left: 1rem; color: #666; font-style: italic;">
        {{ $review->rejection_reason }}
    </blockquote>
    <p>Pode aceder às suas opiniões e editar a mesma para voltar a submetê-la para aprovação.</p>
    <br>
    <p>Com os melhores cumprimentos,<br>A equipa da InovCorp.</p>
</body>
</html>
