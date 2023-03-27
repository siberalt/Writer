CREATE TABLE book
(
    id             INT AUTO_INCREMENT NOT NULL,
    book_id        INT      DEFAULT NULL,
    name           VARCHAR(255) NOT NULL,
    brief          LONGTEXT     NOT NULL,
    page_amount    INT          NOT NULL,
    last_save_date DATETIME DEFAULT NULL,
    is_draft       TINYINT(1) NOT NULL,
    UNIQUE INDEX UNIQ_CBE5A33116A2B381 (
        book_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE person
(
    id   INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 CO
LLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE person_book
(
    person_id INT NOT NULL,
    book_id   INT NOT NULL,
    INDEX     IDX_4103271A217BBB47 (person_id),
    INDEX     IDX_4103271A
        16A2B381 (book_id),
    PRIMARY KEY (person_id, book_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
CREATE TABLE messenger_messages
(
    id           BIGINT AUTO_INCREMENT NOT NULL,
    body         LONGTEXT     NOT NULL,
    headers      LONGTEXT     NOT NULL,
    queue_name
                 VARCHAR(190) NOT NULL,
    created_at   DATETIME     NOT NULL,
    available_at DATETIME     NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    INDEX        IDX _75EA56E0FB7336F0 (queue_name),
    INDEX        IDX_75EA56E0E3BD61CE (available_at),
    INDEX        IDX_75EA56E016BA31DB (delivered_at),
    PRIMARY KEY (
                 id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
ALTER TABLE book
    ADD CONSTRAINT FK_CBE5A33116A2B381 FOREIGN KEY (book_id) REFERENCES book (id);
ALTER TABLE person_book
    ADD CONSTRAINT FK_4103271A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE;
ALTER TABLE person_book
    ADD CONSTRAINT FK_4103271A16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE;
