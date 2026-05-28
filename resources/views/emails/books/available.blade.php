<x-mail::message>
# O livro já está disponível!

Temos boas notícias! O livro **"{{ $book->title }}"**, pelo qual ativou um alerta de disponibilidade, acaba de ser devolvido à biblioteca.

Pode agora proceder à sua requisição através da nossa plataforma antes que outra pessoa o faça.

<x-mail::button :url="route('catalog.livros.show', $book)">
Ver Livro e Requisitar
</x-mail::button>

Atentamente,<br>
A Equipa da Biblioteca InovCorp
</x-mail::message>
