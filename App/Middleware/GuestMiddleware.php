<?php
namespace App\Middleware;

use App\Helper\Helper;

class GuestMiddleware
{
    public function handle(array $params, callable $next)
    {
        if (!isset($_SESSION['user_id']) && !isset($_COOKIE['remember_me'])) {
            return $next($params);
        } else {
            header('Location: ' . Helper::url('dashboard'));
            exit;
        }
    }
}