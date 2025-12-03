<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an auth filter
     * needs to restrict a user's access to a page,
     * it should return an instance of RedirectResponse
     * here.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return ResponseInterface|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Assuming 'isLoggedIn' and 'role' are stored in session
        if (!session()->isLoggedIn || session()->get('role') !== 'admin') {
            return redirect()->to(site_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman admin.');
        }
    }

    /**
     * We aren't doing anything here.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
