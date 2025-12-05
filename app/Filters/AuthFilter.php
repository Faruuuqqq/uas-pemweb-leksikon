<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
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
        if (!session()->isLoggedIn) { // Assuming a 'isLoggedIn' session variable
            return redirect()->to(site_url('login'))->with('error', 'Anda harus login untuk mengakses halaman ini.');
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
