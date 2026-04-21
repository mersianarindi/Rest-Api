<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $image_url;
    public $calories;
    public $ingredients = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // =========================
    // READ ALL
    // =========================
    public function read() {
        $query = "SELECT * FROM {$this->table_name} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // =========================
    // READ ONE
    // =========================
    public function read_one() {
        $query = "SELECT * FROM {$this->table_name} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image_url = $row['image_url'];
            $this->calories = $row['calories'];

            $this->get_ingredients();
            return true;
        }

        return false;
    }

    // =========================
    // GET INGREDIENTS
    // =========================
    private function get_ingredients() {
        $query = "SELECT name FROM ingredients WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $this->ingredients = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->ingredients[] = $row['name'];
        }
    }

    // =========================
    // CREATE
    // =========================
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                  (name, description, price, image_url, calories)
                  VALUES (:name, :description, :price, :image_url, :calories)";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":calories", $this->calories);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();

            if (!empty($this->ingredients)) {
                $this->save_ingredients();
            }

            return true;
        }

        return false;
    }

    // =========================
    // UPDATE
    // =========================
    public function update() {
        $query = "UPDATE {$this->table_name}
                  SET name=:name, description=:description,
                      price=:price, image_url=:image_url, calories=:calories
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":calories", $this->calories);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            $this->update_ingredients();
            return true;
        }

        return false;
    }

    // =========================
    // DELETE
    // =========================
    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    // =========================
    // SEARCH
    // =========================
    public function search($keywords) {
        $query = "SELECT * FROM {$this->table_name}
                  WHERE name LIKE ? OR description LIKE ?
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);

        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);

        $stmt->execute();
        return $stmt;
    }

    // =========================
    // INGREDIENTS HELPERS
    // =========================
    private function save_ingredients() {
        $query = "INSERT INTO ingredients (product_id, name)
                  VALUES (:product_id, :name)";
        $stmt = $this->conn->prepare($query);

        foreach ($this->ingredients as $item) {
            $stmt->bindParam(":product_id", $this->id);
            $stmt->bindParam(":name", $item);
            $stmt->execute();
        }
    }

    private function update_ingredients() {
        $delete = "DELETE FROM ingredients WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($delete);
        $stmt->bindParam(":product_id", $this->id);
        $stmt->execute();

        if (!empty($this->ingredients)) {
            $this->save_ingredients();
        }
    }

    // =========================
    // SANITIZE INPUT
    // =========================
    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->calories = htmlspecialchars(strip_tags($this->calories));
    }
}
?>