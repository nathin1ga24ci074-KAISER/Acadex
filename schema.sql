-- ============================================================
--  ACADEX — Academic Publication Management System
--  Database Schema (MySQL)
-- ============================================================
 
CREATE DATABASE IF NOT EXISTS acadex;
USE acadex;
 
-- ─── INSTITUTIONS ────────────────────────────────────────────
CREATE TABLE institutions (
    institution_id   INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(255) NOT NULL,
    country          VARCHAR(100),
    city             VARCHAR(100),
    website          VARCHAR(255),
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- ─── AUTHORS ─────────────────────────────────────────────────
CREATE TABLE authors (
    author_id        INT AUTO_INCREMENT PRIMARY KEY,
    first_name       VARCHAR(100) NOT NULL,
    last_name        VARCHAR(100) NOT NULL,
    email            VARCHAR(150) UNIQUE,
    institution_id   INT,
    research_area    VARCHAR(255),
    h_index          INT DEFAULT 0,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(institution_id) ON DELETE SET NULL
);
 
-- ─── VENUES ──────────────────────────────────────────────────
CREATE TABLE venues (
    venue_id         INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(255) NOT NULL,
    type             ENUM('Journal', 'Conference', 'Workshop', 'Symposium') NOT NULL,
    publisher        VARCHAR(255),
    impact_factor    DECIMAL(5,3),
    issn             VARCHAR(20),
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- ─── PAPERS ──────────────────────────────────────────────────
CREATE TABLE papers (
    paper_id         INT AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(500) NOT NULL,
    abstract         TEXT,
    keywords         VARCHAR(500),
    doi              VARCHAR(150) UNIQUE,
    publication_year YEAR NOT NULL,
    venue_id         INT,
    pages            VARCHAR(50),
    pdf_url          VARCHAR(255),
    status           ENUM('Published', 'Under Review', 'Preprint', 'Retracted') DEFAULT 'Published',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id) ON DELETE SET NULL,
    FULLTEXT(title, abstract, keywords)
);
 
-- ─── PAPER–AUTHOR (Many-to-Many) ─────────────────────────────
CREATE TABLE paper_authors (
    paper_id         INT NOT NULL,
    author_id        INT NOT NULL,
    author_order     INT DEFAULT 1,          -- 1 = first/lead author
    is_corresponding TINYINT(1) DEFAULT 0,
    PRIMARY KEY (paper_id, author_id),
    FOREIGN KEY (paper_id)  REFERENCES papers(paper_id)  ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(author_id) ON DELETE CASCADE
);
 
-- ─── CITATIONS ───────────────────────────────────────────────
CREATE TABLE citations (
    citation_id      INT AUTO_INCREMENT PRIMARY KEY,
    citing_paper_id  INT NOT NULL,           -- paper that cites
    cited_paper_id   INT NOT NULL,           -- paper being cited
    context          TEXT,                   -- sentence where citation appears
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_citation (citing_paper_id, cited_paper_id),
    FOREIGN KEY (citing_paper_id) REFERENCES papers(paper_id) ON DELETE CASCADE,
    FOREIGN KEY (cited_paper_id)  REFERENCES papers(paper_id) ON DELETE CASCADE,
    CHECK (citing_paper_id <> cited_paper_id)
);
 
-- ─── VIEWS ───────────────────────────────────────────────────
 
-- Full paper details with venue and citation count
CREATE VIEW paper_details AS
SELECT
    p.paper_id,
    p.title,
    p.abstract,
    p.keywords,
    p.doi,
    p.publication_year,
    p.status,
    v.name           AS venue_name,
    v.type           AS venue_type,
    v.impact_factor,
    COUNT(DISTINCT c.citation_id) AS citation_count
FROM papers p
LEFT JOIN venues   v ON p.venue_id = v.venue_id
LEFT JOIN citations c ON c.cited_paper_id = p.paper_id
GROUP BY p.paper_id;
 
-- Author publication summary
CREATE VIEW author_stats AS
SELECT
    a.author_id,
    CONCAT(a.first_name, ' ', a.last_name) AS full_name,
    a.email,
    a.research_area,
    i.name           AS institution,
    COUNT(DISTINCT pa.paper_id) AS paper_count,
    COALESCE(SUM(pd.citation_count), 0) AS total_citations,
    a.h_index
FROM authors a
LEFT JOIN institutions i  ON a.institution_id = i.institution_id
LEFT JOIN paper_authors pa ON a.author_id = pa.author_id
LEFT JOIN paper_details pd ON pa.paper_id = pd.paper_id
GROUP BY a.author_id;
 
-- ─── STORED PROCEDURE: Update h-index ───────────────────────
DELIMITER $$
CREATE PROCEDURE update_h_index(IN p_author_id INT)
BEGIN
    DECLARE h INT DEFAULT 0;
    SELECT COUNT(*) INTO h
    FROM (
        SELECT pd.citation_count
        FROM paper_authors pa
        JOIN paper_details pd ON pa.paper_id = pd.paper_id
        WHERE pa.author_id = p_author_id
          AND pd.citation_count >= (
              SELECT COUNT(*) FROM paper_authors pa2
              JOIN paper_details pd2 ON pa2.paper_id = pd2.paper_id
              WHERE pa2.author_id = p_author_id
          )
    ) sub;
    UPDATE authors SET h_index = h WHERE author_id = p_author_id;
END$$
DELIMITER ;
 
-- ─── TRIGGERS ────────────────────────────────────────────────
 
-- Prevent self-citation (extra safety besides CHECK constraint)
DELIMITER $$
CREATE TRIGGER before_citation_insert
BEFORE INSERT ON citations
FOR EACH ROW
BEGIN
    IF NEW.citing_paper_id = NEW.cited_paper_id THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A paper cannot cite itself';
    END IF;
END$$
DELIMITER ;
 
-- ─── SAMPLE DATA ─────────────────────────────────────────────
 
INSERT INTO institutions (name, country, city, website) VALUES
('MIT',                       'USA',     'Cambridge',  'https://mit.edu'),
('Stanford University',       'USA',     'Stanford',   'https://stanford.edu'),
('IIT Bombay',                'India',   'Mumbai',     'https://iitb.ac.in'),
('University of Cambridge',   'UK',      'Cambridge',  'https://cam.ac.uk'),
('ETH Zurich',                'Switzerland','Zurich',  'https://ethz.ch');
 
INSERT INTO venues (name, type, publisher, impact_factor, issn) VALUES
('Nature',                         'Journal',    'Springer Nature',  69.504, '0028-0836'),
('NeurIPS',                        'Conference', 'NeurIPS Foundation', NULL,  '1049-5258'),
('IEEE Transactions on AI',        'Journal',    'IEEE',              6.750, '2691-4581'),
('ACM SIGMOD',                     'Conference', 'ACM',               NULL,  '0730-8078'),
('Journal of Machine Learning Research', 'Journal','JMLR', 4.995, '1532-4435');
 
INSERT INTO authors (first_name, last_name, email, institution_id, research_area, h_index) VALUES
('Yann',     'LeCun',     'lecun@mit.edu',         1, 'Deep Learning, Computer Vision',     175),
('Fei-Fei',  'Li',        'ffl@stanford.edu',      2, 'Computer Vision, AI',                 90),
('Priya',    'Sharma',    'priya@iitb.ac.in',      3, 'Natural Language Processing',          12),
('Geoffrey', 'Hinton',    'hinton@cam.ac.uk',      4, 'Neural Networks, Cognitive Science',  150),
('Aditya',   'Kumar',     'aditya@ethz.ch',        5, 'Distributed Systems, Databases',       18);
 
INSERT INTO papers (title, abstract, keywords, doi, publication_year, venue_id, status) VALUES
('Attention Is All You Need',
 'We propose a new simple network architecture, the Transformer, based solely on attention mechanisms, dispensing with recurrence and convolutions entirely.',
 'transformer, attention, NLP, deep learning',
 '10.48550/arXiv.1706.03762', 2017, 2, 'Published'),
 
('ImageNet Large Scale Visual Recognition Challenge',
 'The ImageNet Large Scale Visual Recognition Challenge is a benchmark in object category classification and detection on hundreds of object categories and millions of images.',
 'ImageNet, object detection, visual recognition, deep learning',
 '10.1007/s11263-015-0816-y', 2015, 3, 'Published'),
 
('Deep Residual Learning for Image Recognition',
 'We present a residual learning framework to ease training of networks that are substantially deeper than those used previously.',
 'ResNet, residual learning, deep learning, image recognition',
 '10.1109/CVPR.2016.90', 2016, 2, 'Published'),
 
('BERT: Pre-training of Deep Bidirectional Transformers',
 'We introduce BERT, which stands for Bidirectional Encoder Representations from Transformers, designed to pre-train deep bidirectional representations from unlabeled text.',
 'BERT, NLP, pre-training, transformers, language model',
 '10.18653/v1/N19-1423', 2019, 2, 'Published'),
 
('Scalable Distributed Database Architectures',
 'This paper surveys modern approaches to distributed database management systems with focus on consistency, availability, and partition tolerance.',
 'distributed systems, databases, CAP theorem, consistency',
 '10.1145/3318464.3389710', 2022, 4, 'Published');
 
INSERT INTO paper_authors (paper_id, author_id, author_order, is_corresponding) VALUES
(1, 3, 1, 1), (1, 4, 2, 0),
(2, 2, 1, 1),
(3, 1, 1, 1), (3, 4, 2, 0),
(4, 3, 1, 1), (4, 2, 2, 0),
(5, 5, 1, 1);
 
INSERT INTO citations (citing_paper_id, cited_paper_id, context) VALUES
(4, 1, 'Building on the transformer architecture introduced in [1]...'),
(3, 2, 'We use ImageNet [2] as our primary benchmark...'),
(4, 3, 'Our model builds on residual connections from [3]...'),
(5, 4, 'Language embeddings are initialized using BERT [4]...'),
(5, 1, 'Attention mechanisms described in [1] inspired our query layer...');
 