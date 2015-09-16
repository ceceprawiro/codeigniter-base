DROP TABLE IF EXISTS options;
CREATE TABLE options (
    id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    option_key VARCHAR(40) NOT NULL,
    option_value TEXT,
    option_parent TINYINT UNSIGNED,

    PRIMARY KEY (id),
    KEY idx_fk_option_key (option_key),
    KEY idx_fk_option_parent (option_parent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content LONGTEXT,
    created_at DATETIME NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;