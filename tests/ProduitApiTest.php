<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ProduitApiTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        // On simule l'appel que Flutter fera
        $client = static::createClient();
        $response = $client->request('GET', '/api/produits');

        // On vérifie que le serveur répond 200 OK
        $this->assertResponseIsSuccessful();
        
        // On vérifie que le format est bien le JSON-LD attendu par API Platform
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // On vérifie qu'il y a bien des produits dans la réponse
        $this->assertJsonContains([
            '@context' => '/api/contexts/Produit',
            '@id' => '/api/produits',
            '@type' => 'Collection',
        ]);
    }
}