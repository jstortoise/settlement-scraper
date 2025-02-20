<?php

namespace Model;

use PDO;

class Settlement extends Model
{
    public function __construct()
    {
        parent::__construct('settlements');
    }

    public function createTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS {$this->table} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255),
                ticker_symbol VARCHAR(255),
                deadline DATE,
                class_period_start DATE,
                class_period_end DATE,
                settlement_fund INT,
                settlement_hearing_date DATE,
                post_url TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";

        $this->pdo->exec($sql);
    }

    public function getSettlementsInDeadline()
    {
        $query = "
            SELECT *
            FROM {$this->table}
            WHERE deadline >= CURDATE()
            ORDER BY deadline DESC, settlement_fund DESC;
        ";

        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
}