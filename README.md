[![Typing SVG](https://readme-typing-svg.herokuapp.com?color=%2336BCF7&lines=Anatoliy+Gugiyevv)](https://git.io/typing-svg)

#Подключение к БД(Находится в файле dataBase.php)

``` php
    public function connectToDB()
    {
        try {
            $pdo = new PDO("pgsql:host={$this->host};dbname={$this->databaseName};port={$this->port}", $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Подключение прошло успешно\n";
            return $pdo;
        } catch (PDOException $e) {
            echo "Ошибка подключения: " . $e->getMessage();
            return null;
        }
    }
}

$db = new WorkingTemplate();
$db->connect('localhost', 'postgres', '1111', 'test', '5432');
$pdo = $db->connectToDB();
```

#Создание таблиц(Весь код находится в файле sqlDB.php)

```php
$stmt = $pdo->prepare("CREATE TABLE IF NOT EXISTS Products (
            ID SERIAL PRIMARY KEY,
            PRODUCT_ID VARCHAR(50) NOT NULL,
            PRODUCT_NAME VARCHAR(255) NOT NULL,
            PRODUCT_PRICE NUMERIC(10, 2) NOT NULL,
            PRODUCT_ARTICLE VARCHAR(100),
            PRODUCT_QUANTITY INTEGER NOT NULL,
            IS_HIDDEN BOOLEAN NOT NULL DEFAULT FALSE,
            DATE_CREATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
```

#Все функции PHP которые должны были быть реализованы в КР(Находятся в файле get_Products, от которого идет наследование всех функций)

```php

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
