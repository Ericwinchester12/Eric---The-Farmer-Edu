CREATE TABLE IF NOT EXISTS xp_log (
    id_xp      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_aluno   INT UNSIGNED NOT NULL,
    quantidade INT NOT NULL,
    motivo     VARCHAR(255) NOT NULL,
    `data`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_xp),
    INDEX idx_xp_log_aluno (id_aluno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;