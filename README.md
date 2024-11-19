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
```

#Разметка

```php

<?php
require 'get_Products.php';

$host = 'localhost';
$dbname = 'test';
$user = 'postgres';
$password = '1111';

$products = new CProducts($host, $dbname, $user, $password);
$items = $products->getProducts(10); 
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Актуальные товары</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <h1>Актуальные товары</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Дата создания</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr data-product-id="<?php echo htmlspecialchars($item['PRODUCT_ID']); ?>">
                        <td><?php echo htmlspecialchars($item['ID']); ?></td>
                        <td><?php echo htmlspecialchars($item['PRODUCT_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($item['PRODUCT_PRICE']); ?> ₽</td>
                        <td>
                            <button class="decrease">-</button>
                            <span class="quantity"><?php echo htmlspecialchars($item['PRODUCT_QUANTITY']); ?></span>
                            <button class="increase">+</button>
                        </td>
                        <td><?php echo htmlspecialchars($item['DATE_CREATE']); ?></td>
                        <td><button class="hide">Скрыть</button></td>
                    </tr>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Нет доступных товаров.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('.hide').click(function() {
        var row = $(this).closest('tr');
        var productId = row.data('product-id');

        $.ajax({
            url: 'hide_product.php',
            type: 'POST',
            data: { product_id: productId },
            success: function(response) {
                if (response.success) {
                    row.fadeOut();
                } else {
                    alert('Ошибка при скрытии товара.');
                }
            },
            error: function() {
                alert('Произошла ошибка при выполнении запроса.');
            }
        });
    });

    $('.increase').click(function() {
        var row = $(this).closest('tr');
        var quantitySpan = row.find('.quantity');
        var currentQuantity = parseInt(quantitySpan.text());
        quantitySpan.text(currentQuantity + 1);
        updateProductQuantity(row.data('product-id'), currentQuantity + 1);
    });

    $('.decrease').click(function() {
        var row = $(this).closest('tr');
        var quantitySpan = row.find('.quantity');
        var currentQuantity = parseInt(quantitySpan.text());
        if (currentQuantity > 0) {
            quantitySpan.text(currentQuantity - 1);
            updateProductQuantity(row.data('product-id'), currentQuantity - 1);
        }
    });

    function updateProductQuantity(productId, newQuantity) {
        $.ajax({
            url: 'update_quantity.php', 
            type: 'POST',
            data: { product_id: productId, quantity: newQuantity },
            success: function(response) {
                if (!response.success) {
                    alert('Ошибка при обновлении количества товара.');
                }
            },
            error: function() {
                alert('Произошла ошибка при выполнении запроса.');
            }
        });
    }
});""
</script>
</body>
</html>
```
