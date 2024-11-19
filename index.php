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