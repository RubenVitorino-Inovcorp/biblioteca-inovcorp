<x-mail::message>
# Lembrete de Devolução

Olá {{ $loan->user->name }},

Este é um lembrete de que necessita de devolver o livro "{{ $loan->book->title }}" com data prevista para amanhã.

**Detalhes do Livro:**
* **Título:** {{ $loan->book->title }}
* **ISBN:** {{ $loan->book->isbn ?? 'N/A' }}
* **Nº Requisição:** {{ $loan->loan_number }}

Por favor, devolva o livro até a data prevista para evitar multas.

<div style="text-align: center; margin: 20px 0;">
    @if($loan->book->image_path)
        <img src="{{ url($loan->book->image_path) }}" alt="Capa do Livro" style="max-height: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    @endif
</div>



Aguarde o nosso próximo email ou verifique o estado na sua área pessoal.

<x-mail::button :url="route('catalog.requisicoes.index')">
Ver Minhas Requisições
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>