<?php

declare(strict_types=1);

return [
    'home' => ['opened' => 'Comandas abertas'],
    'tab' => [
        'open' => 'Abrir comanda',
        'table_number' => 'Número da mesa',
        'status' => 'Situação da comanda',
        'close' => 'Fechar comanda',
        'for_table' => 'Comanda pra a mesa #%number%',
        'has_unserved_items' => "There're unserved items for this table. You won't be able to close it.",
        'total' => 'Total',
        'amount_paid' => 'Total pago',
        'no_open' => 'Não nenhuma comanda aberta. <a href="%open_tab_url%">Abra uma</a> e faça os pedidos',
    ],
    'item' => [
        'to_serve' => 'Items à servir',
        'served' => 'Servidos',
        'in_preparation' => 'Items em preparo',
        'mark_as_served' => 'Marcar como servido',
        'mark_as_prepared' => 'Marcar como preparado',
        'number' => 'Numero item',
        'description' => 'Descrição',
        'quantity' => 'Quantidade',
        'subtotal' => 'Sub-total',
        'price_unit' => 'Preço unitário',
    ],
    'order' => ['order' => 'Fazer pedido'],
    'chef' => [
        'todo' => 'Cozinha',
        'no_items' => 'Não há items a serem preparados',
    ],
    'waiter' => [
        'todo' => 'Comandas por garçom',
        'waiter' => 'Waiter',
    ],
    'app' => ['name' => 'Comanda Fácil'],
];
