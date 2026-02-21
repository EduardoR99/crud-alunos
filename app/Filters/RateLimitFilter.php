<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    /**
     * Rate limiting simples baseado em cache.
     * Limita por IP + rota para prevenir brute force.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $maxAttempts = (int) ($arguments[0] ?? 10);
        $decayMinutes = (int) ($arguments[1] ?? 1);

        $ip   = $request->getIPAddress();
        $path = $request->getUri()->getPath();
        $key  = 'rate_limit_' . md5($ip . '|' . $path);

        $cache    = \Config\Services::cache();
        $attempts = (int) $cache->get($key);

        if ($attempts >= $maxAttempts) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_TOO_MANY_REQUESTS)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Muitas tentativas. Tente novamente em alguns minutos.',
                ]);
        }

        $cache->save($key, $attempts + 1, $decayMinutes * 60);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
