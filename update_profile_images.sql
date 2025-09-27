-- Update employee profile images
-- Run this SQL script to update the photo paths for all employees

-- Update existing employees with their profile images
UPDATE employees SET photo_path = 'profile/achivida.jpeg' WHERE name LIKE '%Achivida%';
UPDATE employees SET photo_path = 'profile/ilagan.jpeg' WHERE name LIKE '%Ilagan%';
UPDATE employees SET photo_path = 'profile/Viloria.jpeg' WHERE name LIKE '%Viloria%';
UPDATE employees SET photo_path = 'profile/ferrer.jpeg' WHERE name LIKE '%Ferrer%';
UPDATE employees SET photo_path = 'profile/morales.jpeg' WHERE name LIKE '%Morales%';

-- If you have other employees, add their profile images here
-- UPDATE employees SET photo_path = 'profile/[filename].jpeg' WHERE name LIKE '%[EmployeeName]%';

-- Check the updated records
SELECT id, name, position, photo_path FROM employees WHERE photo_path LIKE 'profile/%';
