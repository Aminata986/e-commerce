# API Documentation - E-commerce Laravel

## Base URL
```
http://localhost:8000/api
```

## Authentification
L'API utilise Laravel Sanctum pour l'authentification par token.

### Inscription
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Connexion
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Réponse :**
```json
{
    "message": "Connexion réussie",
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "client"
    }
}
```

### Déconnexion
```http
POST /api/logout
Authorization: Bearer {token}
```

## Catalogue

### Liste des produits
```http
GET /api/products?search=phone&category_id=1&min_price=10&max_price=100&sort_by=price&sort_order=asc&per_page=15
```

**Paramètres :**
- `search` : Recherche par mot-clé
- `category_id` : Filtre par catégorie
- `min_price` / `max_price` : Filtre par prix
- `sort_by` : Tri (name, price, created_at)
- `sort_order` : Ordre (asc, desc)
- `per_page` : Nombre d'éléments par page

### Liste des catégories
```http
GET /api/categories
```

## Panier (Client authentifié)

### Voir le panier
```http
GET /api/cart
Authorization: Bearer {token}
```

### Ajouter un article
```http
POST /api/cart/add
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}
```

### Modifier la quantité
```http
PUT /api/cart/item/{itemId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "quantity": 3
}
```

### Supprimer un article
```http
DELETE /api/cart/item/{itemId}
Authorization: Bearer {token}
```

## Commandes

### Créer une commande (Client)
```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "shipping_address": "123 Rue de la Paix, 75001 Paris",
    "payment_method": "card"
}
```

### Voir une commande (Client)
```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

### Télécharger la facture (Admin)
```http
GET /api/orders/{id}/invoice
Authorization: Bearer {token}
```

## Administration (Admin uniquement)

### Statistiques du tableau de bord
```http
GET /api/dashboard/statistics
Authorization: Bearer {token}
```

**Réponse :**
```json
{
    "total_orders": 150,
    "total_revenue": 15000.50,
    "top_products": [
        {
            "name": "iPhone 15",
            "total_sold": 25
        }
    ],
    "top_customers": [
        {
            "user": {
                "name": "John Doe",
                "email": "john@example.com"
            },
            "order_count": 5,
            "total_spent": 1500.00
        }
    ],
    "total_payments": 15000.50,
    "orders_by_status": [
        {
            "status": "en attente",
            "count": 10
        }
    ],
    "monthly_orders": [
        {
            "year": 2024,
            "month": 1,
            "count": 25,
            "revenue": 2500.00
        }
    ]
}
```

### Gestion des produits
```http
# Créer un produit
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Nouveau Produit",
    "description": "Description du produit",
    "price": 99.99,
    "stock": 50,
    "category_id": 1
}

# Modifier un produit
PUT /api/products/{id}
Authorization: Bearer {token}

# Supprimer un produit
DELETE /api/products/{id}
Authorization: Bearer {token}
```

### Gestion des commandes
```http
# Liste des commandes
GET /api/orders
Authorization: Bearer {token}

# Modifier une commande
PUT /api/orders/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "expédiée",
    "payment_status": "payé",
    "payment_method": "card",
    "shipping_address": "123 Rue de la Paix",
    "total": 150.00
}
```

### Paiements

#### Simuler un paiement en ligne
```http
POST /api/payments/simulate/{orderId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "payment_method": "card"
}
```

#### Marquer comme payé à la livraison
```http
POST /api/payments/delivery/{orderId}
Authorization: Bearer {token}
```

## Codes de réponse

- `200` : Succès
- `201` : Créé avec succès
- `400` : Requête invalide
- `401` : Non authentifié
- `403` : Accès refusé (rôle insuffisant)
- `404` : Ressource non trouvée
- `422` : Erreur de validation
- `500` : Erreur serveur

## Exemples d'utilisation avec cURL

### Inscription
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Connexion
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Ajouter au panier
```bash
curl -X POST http://localhost:8000/api/cart/add \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2
  }'
```

## Notes importantes

1. **Rôles** : L'API distingue les rôles `client` et `admin`
2. **Validation** : Toutes les requêtes sont validées côté serveur
3. **Sécurité** : Les routes sensibles sont protégées par authentification et autorisation
4. **Statuts** : Les commandes ont des statuts stricts (en attente, expédiée, livrée, annulée)
5. **Paiements** : Simulation disponible pour les tests

## Installation et configuration

1. Installer les dépendances :
```bash
composer install
```

2. Configurer la base de données dans `.env`

3. Migrer et seeder :
```bash
php artisan migrate
php artisan db:seed
```

4. Installer Sanctum :
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

5. Démarrer le serveur :
```bash
php artisan serve
``` 