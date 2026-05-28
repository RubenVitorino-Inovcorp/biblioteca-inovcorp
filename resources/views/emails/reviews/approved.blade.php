<x-mail::message>
# A sua opinião foi aprovada!

Olá {{ $review->user->name }},

A sua opinião sobre o livro **{{ $review->book->title }}** foi analisada e **aprovada** pela nossa equipa de moderação.

Ela já se encontra visível para todos os utilizadores no nosso catálogo!

Obrigado por partilhar a sua opinião connosco!

Com os melhores cumprimentos,<br>
A equipa da InovCorp.
</x-mail::message>
