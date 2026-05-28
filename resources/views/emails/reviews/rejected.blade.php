<x-mail::message>
# Atualização sobre a sua opinião

Olá {{ $review->user->name }},

A sua opinião sobre o livro **{{ $review->book->title }}** foi analisada pela nossa equipa de moderação.

Infelizmente, a sua opinião foi **recusada** pelo seguinte motivo:

<x-mail::panel>
{{ $review->rejection_reason }}
</x-mail::panel>

Pode aceder às suas opiniões e editar a mesma para voltar a submetê-la para aprovação.

Com os melhores cumprimentos,<br>
A equipa da InovCorp.
</x-mail::message>
