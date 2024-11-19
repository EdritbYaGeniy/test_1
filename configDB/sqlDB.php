<?php 
require 'dataBase.php';

$db = new WorkingTemplate();
$db->connect("localhost", "postgres", "1111", "test", "5432");

$pdo = $db->connectToDB();

if ($pdo) {
    try {
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

        $stmt->execute();
        echo "Таблица Products успешно создана.\n";
    } catch (PDOException $e) {
        echo "Ошибка при создании таблицы: " . $e->getMessage();
    }
} else {
    echo "Ошибка подключения к базе данных..";
}