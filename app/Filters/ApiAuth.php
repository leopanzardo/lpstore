<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\ApiKeyModel;

class ApiAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');
        
        if (empty($apiKey)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'API Key requerida']);
        }
        
        // Validar API Key
        $apiKeyModel = new ApiKeyModel();
        $validKey = $apiKeyModel->validateKey($apiKey);
        
        if (!$validKey) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'API Key inválida']);
        }
        
        // Guardar el client_id para usarlo en los controladores
        $request->apiClientId = $validKey->client_id;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada
    }
}