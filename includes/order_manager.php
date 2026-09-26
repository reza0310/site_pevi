<?php

require_once __DIR__ . '/db.php';

class OrderManager
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->get_pdo();
    }

    /**
     * Creates an order with items using a database transaction.
     * $items = [ ['product_id' => 1, 'quantity' => 2, 'unit_price' => 1500], ... ]
     */
    public function create_order(array $items, int $total_price, string $price_unit = DEFAULT_MONETARY_UNIT): int
    {
        $this->pdo->beginTransaction();

        try {
            // Insert parent order
            $sql_order = "INSERT INTO orders (total_price, price_unit, state, ordered_at)
                         VALUES (:total_price, :price_unit, :state, NOW())";
            $stmt_order = $this->pdo->prepare($sql_order);
            $stmt_order->execute([
                'total_price' => $total_price,
                'price_unit' => $price_unit,
                'state' => OrderState::PENDING,
            ]);

            $order_id = (int) $this->pdo->lastInsertId();

            // Insert each item into order_items
            $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, unit_price_amount)
                        VALUES (:order_id, :product_id, :quantity, :unit_price_amount)";
            $stmtItem = $this->pdo->prepare($sqlItem);

            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id'          => $order_id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price_amount' => $item['unit_price'],
                ]);
            }

            $this->pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Updates order state.
     */
    public function update_order_status(int $order_id, string $newState): bool
    {
        if (!in_array($newState, OrderStatus::ALL, true)) {
            throw new InvalidArgumentException("Invalid state: {$newState}");
        }

        $sql = ($newState === OrderStatus::DELIVERED)
            ? "UPDATE orders SET state = :state, delivered_at = NOW() WHERE id = :id"
            : "UPDATE orders SET state = :state WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'state' => $newState,
            'id'    => $order_id,
        ]);
    }

    /**
     * Replaces products in an order.
     */
    /*
    public function update_order_items(int $order_id, array $new_items, int $new_price_amount): void
    {
        $this->pdo->beginTransaction();

        try {
            // Update main order amount
            $stmt_order = $this->pdo->prepare("UPDATE orders SET price_amount = :amount WHERE id = :id");
            $stmt_order->execute(['amount' => $new_price_amount, 'id' => $order_id]);

            // Clear old items
            $stmtDelete = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
            $stmtDelete->execute(['order_id' => $order_id]);

            // Insert new items
            $stmtItem = $this->pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price_amount)
                 VALUES (:order_id, :product_id, :quantity, :unit_price_amount)"
            );

            foreach ($new_items as $item) {
                $stmtItem->execute([
                    'order_id'          => $order_id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price_amount' => $item['unit_price'],
                ]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    */

    /**
     * Retrieves an order along with its associated items and product titles.
     */
    public function get_order_details(int $order_id): ?array
    {
        $sql_order = "SELECT * FROM orders WHERE id = :id";
        $stmt_order = $this->pdo->prepare($sql_order);
        $stmt_order->execute(['id' => $order_id]);
        $order = $stmt_order->fetch();

        if (!$order) {
            return null;
        }

        $sqlItems = "
            SELECT oi.id AS item_id, oi.product_id, oi.quantity, oi.unit_price_amount, p.title
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = :order_id
        ";
        $stmtItems = $this->pdo->prepare($sqlItems);
        $stmtItems->execute(['order_id' => $order_id]);

        $order['items'] = $stmtItems->fetchAll();

        return $order;
    }
}

?>
