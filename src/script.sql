-- Insertion des devises
INSERT INTO devise (id,nom, valeur) VALUES
(1,'EUR', 1),   -- Euro
(2,'USD', 1),   
(3,'MGA', 1); -- Ariary malgache

-- Insertion des cryptomonnaies
INSERT INTO crypto (id,nom,symbol) VALUES
(1,'Bitcoin','BTC'),
(2,'Ethereum','ETH');

-- Configuration des paires de devises
INSERT INTO config_devise (id,devise_id, devise_base_id, valeur) VALUES 
(1,1, 1, 1), -- 1 EUR = 4900 MGA
(2,2, 1, 1.10),    -- 1 EUR = 1.10 USD
(3,3, 1, 4500);


INSERT INTO ADMIN VALUES(default, 'admin','test');
CREATE VIEW crypto_transaction_view AS
SELECT 
    id,
    crypto_id,
    devise_id,
    id_user,
    entre,
    sortie,
    crypto_cours,
    datetime,
    CASE 
        WHEN sortie = 0 THEN 'buy'
        WHEN entre = 0 THEN 'sell'
        ELSE 'unknown'
    END AS type
FROM 
    crypto_transaction;
    