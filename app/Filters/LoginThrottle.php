<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class LoginThrottle implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Throttle hanya untuk POST (submit login)
        if (! $request->is('post')) {
            return;
        }

        $throttler = Services::throttler();

        $ip   = $request->getIPAddress();
        $user = (string) ($request->getPost('username') ?? '');
        $key  = 'login_' . sha1($ip . '|' . mb_strtolower($user));

        // Maksimal 5 percobaan per 10 menit untuk kombinasi IP+username
        $allowed = $throttler->check($key, 5, MINUTE * 10);

        if (! $allowed) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(429)
                    ->setJSON(['status' => 'error', 'message' => 'Terlalu banyak percobaan. Coba lagi beberapa menit.']);
            }

            return redirect()->back()->with('error', 'Terlalu banyak percobaan. Silakan coba lagi beberapa menit.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
