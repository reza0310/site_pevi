<?php

require_once dirname(__DIR__, 1) . '/config/creds.php';

const BASE_URL = 'https://marketplace.philibertnet.com/api';

class PhilibertAPI {
    public static function update_stock(string $product_id, int $new_stock): void {
        // Update Philibert stock
        HTTP::patch(
            url: BASE_URL . '/merchant/products/stock',
            body: json_encode([[
                'reference' => product_id_to_string($product_id),
                'stock' => $new_stock,
            ]]),
            header: [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . PHILIBERT_API_KEY,
            ]
        );
    }
}

?>
