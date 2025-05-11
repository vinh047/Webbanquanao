<?php
class DBConnect
{
    private static $instance = null;
    private $pdo;

    private $host = 'localhost';
    private $dbname = 'db_web_quanao';
    private $username = 'root';
    private $password = '';

    // Constructor private để dùng Singleton
    private function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Kết nối thất bại: " . $e->getMessage());
        }
    }

    // Singleton: chỉ tạo 1 thể hiện
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DBConnect();
        }
        return self::$instance;
    }

    // Lấy PDO gốc (nếu cần truy vấn thủ công)
    public function getConnection()
    {
        return $this->pdo;
    }

    // Hàm tiện dụng: SELECT
    public function select($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Hàm tiện dụng: SELECT 1 dòng
    public function selectOne($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // Thêm vô DBConnect.php
    public function count($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }


    // Hàm tiện dụng: INSERT, UPDATE, DELETE
    public function execute($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Hàm INSERT tiện dụng
    public function insert($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
        return $this->execute($sql, array_values($data));
    }

    // Hàm UPDATE tiện dụng
    public function update($table, $data, $whereClause, $whereParams = [])
    {
        $setClause = implode(", ", array_map(fn($key) => "$key = ?", array_keys($data)));
        $sql = "UPDATE `$table` SET $setClause WHERE $whereClause";
        return $this->execute($sql, array_merge(array_values($data), $whereParams));
    }

    public function getEnumValues($table, $column)
    {
        $stmt = $this->pdo->query("SHOW COLUMNS FROM `$table` WHERE Field = '$column'");
        $row = $stmt->fetch();
        if (!$row || strpos($row['Type'], 'enum') === false) return [];

        $enumStr = $row['Type']; // enum('A','B','C')
        $enumStr = str_replace(["enum(", ")"], "", $enumStr);
        $enumStr = str_getcsv($enumStr, ",", "'"); // tách chuỗi an toàn

        return $enumStr;
    }
}
