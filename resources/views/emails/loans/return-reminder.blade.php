<x-mail::message>
# Lembrete de Devolução

Olá {{ $loan->user->name }},

Este é um lembrete de que necessita de devolver o livro "{{ $loan->book->title }}" com data prevista para amanhã.

**Detalhes do Livro:**
* **Título:** {{ $loan->book->title }}
* **ISBN:** {{ $loan->book->isbn ?? 'N/A' }}
* **Nº Requisição:** {{ $loan->loan_number }}

<div style="text-align: center; margin: 20px 0;">
    @if($loan->book->image_path)
        <img src="{{ url($loan->book->image_path) }}" alt="Capa do Livro" style="max-height: 200px; border-radius: 8px;">
    @endif
</div>

Por favor, devolva o livro até à data prevista para evitar multas.

<x-mail::button :url="route('catalog.requisicoes.index')">
Ver Minhas Requisições
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>