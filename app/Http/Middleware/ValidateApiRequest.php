<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérification du Content-Type pour les requêtes POST/PUT
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            if (!$request->isJson() && !$request->hasHeader('Content-Type')) {
                return response()->json([
                    'error' => 'Content-Type doit être application/json',
                    'message' => 'Les requêtes POST/PUT doivent être en JSON'
                ], 400);
            }
        }

        // Vérification de la taille des requêtes
        if ($request->getContent() && strlen($request->getContent()) > 1000000) {
            return response()->json([
                'error' => 'Requête trop volumineuse',
                'message' => 'La taille de la requête ne doit pas dépasser 1MB'
            ], 413);
        }

        // Vérification des champs obligatoires pour certaines routes
        $this->validateRequiredFields($request);

        return $next($request);
    }

    private function validateRequiredFields(Request $request)
    {
        $route = $request->route();
        if (!$route) return;

        $routeName = $route->getName();
        $method = $request->method();

        // Validation spécifique selon la route
        switch ($routeName) {
            case 'api.register':
                $this->validateRegistration($request);
                break;
            case 'api.login':
                $this->validateLogin($request);
                break;
            case 'api.cart.add':
                $this->validateCartAdd($request);
                break;
        }
    }

    private function validateRegistration(Request $request)
    {
        $required = ['name', 'email', 'password', 'password_confirmation'];
        $this->checkRequiredFields($request, $required, 'Inscription');
    }

    private function validateLogin(Request $request)
    {
        $required = ['email', 'password'];
        $this->checkRequiredFields($request, $required, 'Connexion');
    }

    private function validateCartAdd(Request $request)
    {
        $required = ['product_id', 'quantity'];
        $this->checkRequiredFields($request, $required, 'Ajout au panier');
    }

    private function checkRequiredFields(Request $request, array $fields, string $context)
    {
        $missing = [];
        foreach ($fields as $field) {
            if (!$request->has($field) || $request->input($field) === null || $request->input($field) === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            abort(422, json_encode([
                'error' => 'Champs manquants',
                'message' => "Pour {$context}, les champs suivants sont obligatoires : " . implode(', ', $missing),
                'missing_fields' => $missing
            ]));
        }
    }
} 