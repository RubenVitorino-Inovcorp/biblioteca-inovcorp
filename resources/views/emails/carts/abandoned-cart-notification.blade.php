<x-mail::message>
# Não deixe os livros para trás!

Olá {{ $order->user?->name ?? 'Cliente' }},

A sua encomenda **{{ $order->order_number ?? 'sem número' }}** não foi concluída.

Para continuar com a sua encomenda, por favor clique no botão abaixo:

<x-mail::button :url="route('catalog.carrinho.index')">
Continuar Encomenda
</x-mail::button>

Se precisar de ajuda, por favor, entre em contacto connosco. Pode responder a este email ou entrar em contato connosco através do nosso site.

Se não deseja continuar com a sua encomenda, por favor, ignore este email.

Com os melhores cumprimentos,<br>
A equipa da Biblioteca InovCorp.
</x-mail::message>
