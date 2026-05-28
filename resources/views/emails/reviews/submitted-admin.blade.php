<x-mail::message>
# Nova Opinião Submetida

Uma nova opinião foi submetida pelo cidadão **{{ $review->user->name }}** e aguarda moderação.

**Livro:** {{ $review->book->title }}
**Cidadão:** {{ $review->user->name }} ({{ $review->user->email }})
**Classificação:** {{ $review->rating }}/10
**Título:** {{ $review->review_title }}

<x-mail::panel>
"{{ Str::limit($review->review_text, 150) }}"
</x-mail::panel>

<x-mail::button :url="route('opinioes.show', $review->id)">
Ver Detalhes da Opinião
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
