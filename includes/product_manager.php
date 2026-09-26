<?php

require_once __DIR__ . '/db.php';

class ProductManager
{
    private PDO $pdo;

    public function __construct(Database $db)
    {
        $this->pdo = $db->get_pdo();
    }

    /**
     * Creates a new product and return its id.
     */
    public function create_product(
        string $slug,
        string $title,
        string $description,
        string $thumbnail,
        string $price_unit,
        int $price_amount,
        int $stock
    ): int {
        if (!in_array(strtolower($price_unit), MONETARY_UNITS, true)) {
            throw new InvalidArgumentException("Unité monétaire invalide : {$price_unit}");
        }

        $sql = "INSERT INTO products (slug, title, description, thumbnail, price_unit, price_amount, stock, state)
                VALUES (:slug, :title, :description, :thumbnail, :price_unit, :price_amount, :stock, 'active')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'slug'         => $slug,
            'title'        => $title,
            'description'  => $description,
            'thumbnail'    => $thumbnail,
            'price_unit'   => strtolower($price_unit),
            'price_amount' => $price_amount,
            'stock'        => $stock,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Updates product stock by adding $delta (can be positive or negative).
     */
    public function update_product_stock(int $product_id, int $delta): bool
    {
        // Mise à jour atomique pour éviter les conditions de concurrence (race conditions)
        $sql = "UPDATE products SET stock = stock + :delta WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'delta' => $delta,
            'id'    => $product_id,
        ]);
    }

    /**
     * Retrieves a product stock.
     */
    public function get_product_stock(int $product_id): int
    {
        $sql = "SELECT stock FROM products WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $product_id]);

        $product = $stmt->fetch();

        return $product['stock'];
    }

    /**
     * Updates product details dynamically based on provided parameters.
     */
    public function update_product_details(
        int $product_id,
        ?string $slug = null,
        ?string $title = null,
        ?string $description = null,
        ?string $thumbnail = null,
        ?string $price_unit = null,
        ?int $price_amount = null,
        ?string $state = null
    ): bool {
        $fields = [];
        $params = ['id' => $product_id];

        if ($slug !== null) {
            $fields[] = "slug = :slug";
            $params['slug'] = $slug;
        }

        if ($title !== null) {
            $fields[] = "title = :title";
            $params['title'] = $title;
        }

        if ($description !== null) {
            $fields[] = "description = :description";
            $params['description'] = $description;
        }

        if ($thumbnail !== null) {
            $fields[] = "thumbnail = :thumbnail";
            $params['thumbnail'] = $thumbnail;
        }

        if ($price_unit !== null) {
            $allowed_units = MONETARY_UNITS;
            if (!in_array(strtolower($price_unit), $allowed_units, true)) {
                throw new InvalidArgumentException("Unité monétaire invalide : {$price_unit}");
            }
            $fields[] = "price_unit = :price_unit";
            $params['price_unit'] = strtolower($price_unit);
        }

        if ($price_amount !== null) {
            $fields[] = "price_amount = :price_amount";
            $params['price_amount'] = $price_amount;
        }

        if ($state !== null) {
            $allowed_states = ProductState::ALL;
            if (!in_array(strtolower($state), $allowed_states, true)) {
                throw new InvalidArgumentException("État de produit invalide : {$state}");
            }
            $fields[] = "state = :state";
            $params['state'] = strtolower($state);
        }

        // Si aucun champ n'est transmis à modifier
        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Retrieves a product by its id.
     */
    public function get_product_details(int $product_id): ?array
    {
        $sql = "SELECT id, slug, title, description, thumbnail, price_unit, price_amount, stock, state, created_at
                FROM products
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $product_id]);

        $product = $stmt->fetch();

        return $product ?: null;
    }
}
