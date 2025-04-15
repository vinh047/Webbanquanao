<?php
class DBConnect {
    private static $instance = null;
    private $pdo;

    private $host = 'localhost';
    private $dbname = 'db_web_quanao';
    private $username = 'root';
    private $password = '';

    // Constructor private để dùng Singleton
    private function __construct() {
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
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DBConnect();
        }
        return self::$instance;
    }

    // Lấy PDO gốc (nếu cần truy vấn thủ công)
    public function getConnection() {
        return $this->pdo;
    }

    // Hàm tiện dụng: SELECT
    public function select($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Hàm tiện dụng: SELECT 1 dòng
    public function selectOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    // Hàm tiện dụng: INSERT, UPDATE, DELETE
    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
}
?>
