<?php

// Monetary units codes are normalized in ISO 4217:
// https://en.wikipedia.org/wiki/ISO_4217
const MONETARY_UNITS = [
    'EUR', // Euro (eurozone countries)
    'USD', // US dollar (United States)
    'GBP', // Pound Sterling (United Kingdom)
    'JPY', // Japanese Yen (Japan)
    'AUD', // Australian dollar (Australia)
    'CAD', // Canadian dollar (Canada)
    'CHF', // Swiss franc (Switzerland)
    'CNY', // Yuan Renminbi (China)
    'INR', // Indian Rupee (India)
];

const DEFAULT_MONETARY_UNIT = 'EUR';


class OrderStatus {
    const PENDING = 'pending';
    const ACCEPTED = 'accepted';
    const REFUSED = 'refused';
    const CANCELLED = 'cancelled';
    const SHIPPED = 'shipped';
    const REFUNDED = 'refunded';
    const DELIVERED = 'delivered';

    const ALL = [
        PENDING,
        ACCEPTED,
        REFUSED,
        CANCELLED,
        SHIPPED,
        REFUNDED,
        DELIVERED,
    ];
}


class ProductState {
    const ACTIVE = 'active';
    const DISCONTINUED = 'discontinued';

    const ALL = [
        ACTIVE,
        DISCONTINUED,
    ];
}


class Database
{
    private PDO $pdo;

    private static function list_to_sql_enum(array $values): string {
        $result = '';
        foreach ($values as $value) {
            if (!empty($result)) {
                $result .= ', ';
            }
            $result .= '\'' . $value . '\'';
        }
        return $result;
    }

    public function __construct(string $host, string $dbname, string $user, string $pass)
    {
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function get_pdo(): PDO
    {
        return $this->pdo;
    }

    public function init_database(): void
    {
        // Products table
        $sqlProducts = "
        CREATE TABLE IF NOT EXISTS products (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(255) NOT NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            thumbnail VARCHAR(255) NULL,
            price_unit ENUM(".list_to_sql_enum(MONETARY_UNITS).") NOT NULL DEFAULT '".DEFAULT_MONETARY_UNIT."',
            price_amount BIGINT NOT NULL,
            stock BIGINT NOT NULL DEFAULT 0,
            state ENUM(".list_to_sql_enum(ProductState::ALL).") NOT NULL DEFAULT '".ProductState::ACTIVE."',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        // Orders table
        $sqlOrders = "
        CREATE TABLE IF NOT EXISTS orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            price_unit ENUM(".list_to_sql_enum(MONETARY_UNITS).") NOT NULL DEFAULT '".DEFAULT_MONETARY_UNIT."',
            price_amount BIGINT NOT NULL,
            state ENUM(".list_to_sql_enum(OrderStatus::ALL).") NOT NULL DEFAULT '".OrderStatus::PENDING."',
            ordered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            delivered_at DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        // Order items table
        $sqlOrderItems = "
        CREATE TABLE IF NOT EXISTS order_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            quantity INT UNSIGNED NOT NULL,
            unit_price_amount BIGINT NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->pdo->exec($sqlProducts);
        $this->pdo->exec($sqlOrders);
        $this->pdo->exec($sqlOrderItems);
    }
}

?>
