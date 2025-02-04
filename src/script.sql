-- Insertion des devises
INSERT INTO devise (nom, valeur) VALUES
('EUR', 1),   -- Euro
('USD', 1),   
('MGA', 1); -- Ariary malgache

-- Insertion des cryptomonnaies
INSERT INTO crypto (nom) VALUES
('Bitcoin'),
('Ethereum');

-- Configuration des paires de devises
INSERT INTO config_devise (devise_id, devise_base_id, valeur) VALUES 
(1, 1, 1), -- 1 EUR = 4900 MGA
(2, 1, 1.10),    -- 1 EUR = 1.10 USD
(3, 1, 4500);

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
    