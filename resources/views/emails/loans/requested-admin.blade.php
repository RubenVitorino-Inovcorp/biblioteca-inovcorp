<x-mail::message>
# Nova Requisição de Empréstimo

O utilizador {{ $loan->user->name }} registou uma nova requisição de empréstimo.

<div style="text-align: center; margin: 20px 0;">
    @if($loan->book->image_path)
        <img src="{{ url($loan->book->image_path) }}" alt="Capa do Livro" style="max-height: 200px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    @endif
</div>

**Detalhes do Livro:**
* **Título:** {{ $loan->book->title }}
* **ISBN:** {{ $loan->book->isbn ?? 'N/A' }}
* **Nº Requisição:** {{ $loan->loan_number }}

<x-mail::button :url="route('requisicoes.show', $loan->id)">
Ver Requisição
</x-mail::button>

</x-mail::message>