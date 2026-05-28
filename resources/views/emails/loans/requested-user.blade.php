<x-mail::message>
# Confirmação de Pedido

Olá {{ $loan->user->name }},

O seu pedido de requisição foi registado com sucesso e encontra-se a aguardar aprovação.

<div style="text-align: center; margin: 20px 0;">
    @if($loan->book->image_path)
        <img src="{{ url($loan->book->image_path) }}" alt="Capa do Livro" style="max-height: 200px; border-radius: 8px;">
    @endif
</div>

**Detalhes do Livro:**
* **Título:** {{ $loan->book->title }}
* **ISBN:** {{ $loan->book->isbn ?? 'N/A' }}
* **Nº Requisição:** {{ $loan->loan_number }}

Aguarde o nosso próximo email ou verifique o estado na sua área pessoal.

<x-mail::button :url="route('catalog.requisicoes.index')">
Ver Minhas Requisições
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>