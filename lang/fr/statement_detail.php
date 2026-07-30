<?php


use App\Enums\StatementDetail;

return [
    StatementDetail::ITEMS_SALE           => 'Vente d\'articles',
    StatementDetail::DELIVERY_FEE_AND_TIP => 'Frais de livraison et pourboire',
    StatementDetail::RELEASE_PAYOUT       => 'Libération du paiement',
    StatementDetail::REVERSE_PAYOUT       => 'Annulation du paiement',
    StatementDetail::SERVICE_FEE          => 'Frais de service et de plateforme'
];
