<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class GlobalApiTest extends ApiTestCase
{
    // On garde l'option pour éviter les dépréciations
    protected static ?bool $alwaysBootKernel = true;

    public function testAllRoutesAreResponsive(): void
    {
        $client = static::createClient();

        $publicUrls = [
            '/api/produits',
            '/api/entreprises',
            '/api/categories',
            '/api/prixes'
        ];

        foreach ($publicUrls as $url) {
            $client->request('GET', $url);
            $this->assertResponseIsSuccessful();
            // On vérifie que c'est bien du JSON-LD pour Flutter
            $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        }

        // Vérification de la sécurité
        $client->request('GET', '/api/commandes');
        $this->assertResponseStatusCodeSame(401);
    }
}