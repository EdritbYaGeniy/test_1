<?php
interface PdoDB
{
    public function connect($host, $user, $password, $databaseName, $port);
}

class WorkingTemplate implements PdoDB
{
    private $host;
    private $user;
    private $password;
    private $databaseName;
    private $port;

    public function connect($host, $user, $password, $databaseName, $port)
    {
        $this->host = $host;
        $this->user = $user; 
        $this->password = $password; 
        $this->databaseName = $databaseName; 
        $this->port = $port; 
    }

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