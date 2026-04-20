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

    // Read all products
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single product
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->image_url = $row['image_url'];
            $this->calories = $row['calories'];
            
            // Get ingredients
            $this->getIngredients();
            return true;
        }
        return false;
    }

    // Get ingredients for a product
    public function getIngredients() {
        $query = "SELECT name FROM ingredients WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $this->ingredients = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->ingredients[] = $row['name'];
        }
    }

    // Create product
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET name=:name, description=:description, 
                      price=:price, image_url=:image_url, calories=:calories";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->calories = htmlspecialchars(strip_tags($this->calories));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":calories", $this->calories);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Save ingredients
            if(!empty($this->ingredients)) {
                $this->saveIngredients();
            }
            return true;
        }
        return false;
    }

    // Save ingredients
    private function saveIngredients() {
        $query = "INSERT INTO ingredients (product_id, name) VALUES (:product_id, :name)";
        $stmt = $this->conn->prepare($query);
        
        foreach($this->ingredients as $ingredient) {
            $stmt->bindParam(":product_id", $this->id);
            $stmt->bindParam(":name", $ingredient);
            $stmt->execute();
        }
    }

    // Update product
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name=:name, description=:description, 
                      price=:price, image_url=:image_url, calories=:calories
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->calories = htmlspecialchars(strip_tags($this->calories));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":calories", $this->calories);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            // Update ingredients
            $this->updateIngredients();
            return true;
        }
        return false;
    }

    // Update ingredients
    private function updateIngredients() {
        // Delete old ingredients
        $query = "DELETE FROM ingredients WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->id);
        $stmt->execute();
        
        // Insert new ingredients
        if(!empty($this->ingredients)) {
            $this->saveIngredients();
        }
    }

    // Delete product
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Search products
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE name LIKE ? OR description LIKE ?
                  ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->execute();
        
        return $stmt;
    }
}
?>