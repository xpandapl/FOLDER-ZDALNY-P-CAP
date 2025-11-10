-- Tymczasowy rollback kolumny active
-- Uruchom to w bazie danych żeby sprawdzić czy problem jest w kolumnie active

-- Usuń indeks
ALTER TABLE `employees` DROP INDEX `employees_active_index`;

-- Usuń kolumnę
ALTER TABLE `employees` DROP COLUMN `active`;
