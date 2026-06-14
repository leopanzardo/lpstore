<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Obtener el rol del header
        $userRole = $request->getHeaderLine('X-LPUSER-ROLE');
        
        // Si no hay rol, denegar acceso
        if (empty($userRole)) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['error' => 'Rol de usuario no especificado']);
        }
        
        // Si se especificaron roles requeridos, verificar
        if ($arguments && !in_array($userRole, $arguments)) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['error' => 'No tienes permiso para acceder a este recurso']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada
    }
}