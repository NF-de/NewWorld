# 1. On supprime l'ancienne base de test (pour être sûr de repartir à zéro)
php bin/console doctrine:database:drop --env=test --force --if-exists

# 2. On recrée la base de test vide
php bin/console doctrine:database:create --env=test

# 3. On crée la structure (les tables) directement à partir de vos entités
php bin/console doctrine:schema:create --env=test

# 4. Pour lancer les tests 
php bin/phpunit 

# 5. Pour lancer des tests spécifiques
php bin/phpunit tests/GlobalApiTest.php