<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredRole = $arguments[0] ?? null;
        $currentRole = session()->get('role');

        if ($requiredRole === null || $currentRole === $requiredRole) {
            return;
        }

        session()->setFlashdata('error', 'Akses ditolak.');
        return redirect()->to('dashboard');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
