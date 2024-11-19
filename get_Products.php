<?php
class CProducts {
    private $pdo;

    public function __construct($host, $dbname, $user, $password, $port = '5432') {
        $this->pdo = new PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getProducts($limit = 10) {
        $stmt = $this->pdo->prepare("SELECT * FROM Products WHERE IS_HIDDEN = FALSE ORDER BY DATE_CREATE DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hideProduct($productId) {
        $stmt = $this->pdo->prepare("UPDATE Products SET IS_HIDDEN = TRUE WHERE PRODUCT_ID = :productId");
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProductQuantity($productId, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE Products SET PRODUCT_QUANTITY = :quantity WHERE PRODUCT_ID = :productId");
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}""
?>