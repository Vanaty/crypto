-- Insertion des devises
INSERT INTO devise (nom, valeur) VALUES
('EUR', 1),   -- Euro
('USD', 1),   -- Dollar US
('MGA', 1); -- Ariary malgache

-- Insertion des cryptomonnaies
INSERT INTO crypto (nom) VALUES
('Bitcoin'),
('Ethereum');

-- Configuration des paires de devises
INSERT INTO config_devise (devise_id, devise_base_id, valeur) VALUES 
(1, 1, 1), -- 1 EUR = 4900 MGA
(2, 1, 2),    -- 1 EUR = 1.10 USD
(3, 1, 4500);

-- Insertion des cours des cryptos
INSERT INTO crypto_cours (devise_id, crypto_id, cours, datetime) VALUES
(3, 1, 35000, NOW()), -- 1 BTC = 35000 EUR
(3, 2, 2800, NOW());  -- 1 ETH = 2800 USD

-- Insertion des transactions utilisateur
INSERT INTO user_transaction (id_user, entre, sortie, devise_id) VALUES
(1, 1000.00000000, 0.00000000, 1),
INSERT INTO user_transaction (id_user, entre, sortie, devise_id) VALUES
(1, 1000.00000000, 0.00000000, 2);
INSERT INTO user_transaction (id_user, entre, sortie, devise_id) VALUES
(1, 1000.00000000, 0.00000000, 3); 
(2, 0.00000000, 500.00000000, 2);  

-- Insertion des transactions crypto
INSERT INTO crypto_transaction (id_user, crypto_id, entre, sortie, devise_id) VALUES
(1, 1, 1, 0.00000000, 3), -- Achat de 0.005 BTC en EUR
(2, 2, 0.00000000, 1, 3); -- Vente de 0.2 ETH en USD

-- Insertion de la configuration des tokens
INSERT INTO config_token (duree_de_vie_token) VALUES (3600); -- Durée de vie de 1 heure

-- Insertion des tokens utilisateurs
INSERT INTO user_token (id_user, token, temps_expiration) VALUES
(1, 'token123', NOW() + INTERVAL '1 HOUR'), -- Token pour l'utilisateur 1
(2, 'token456', NOW() + INTERVAL '2 HOURS'); -- Token pour l'utilisateur 2


select sum(entre * cf.valeur) - sum(sortie * cf.valeur)from user_transaction ut join config_devise cf on ut.devise_id = cf.devise_id where cf.devise_base = 1;