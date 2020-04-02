<?php

namespace App\Controllers;

use App\Models\User;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthController extends BaseController {

    protected $title = "Inicia sesión - Tickera.com";

    public function getLogin(){
        $userId = $_SESSION['userId'] ?? null;
        if($userId){
            $isAdmin = $_SESSION['isAdmin'];
            $response = $isAdmin ? new RedirectResponse('homeAdmin') : new RedirectResponse('home');
            return $response;
        }
        return $this->renderHTML('login.twig');
    }

    private function badCredentials(){
        return "El username/email o la contraseña son incorrectos.";
    }

    private function userExists($usernameOrEmail){
        $user = User::where('username', $usernameOrEmail)->first();
        return $user;
    }

    public function postLogin($request){
        $parsedData = $request->getParsedBody();
        $responseMessage = null;
        
        $user = $this->userExists($parsedData['username']);
        if($user){
            $validPassword = \password_verify($parsedData['password'], $user->password);
            if($validPassword){
                $responseMessage = "Bienvenido a tickera.";
                $_SESSION['userId'] = $user->id;
                $_SESSION['isAdmin'] = (bool)$user->isAdmin;
                $response = $user->isAdmin ? new RedirectResponse('home/admin') : new RedirectResponse('home');
                return $response;
            }
            else{
                $responseMessage = $this->badCredentials();
            }
        }
        else{
            $responseMessage = $this->badCredentials();
        }

        return $this->renderHTML('login.twig', [
            'responseMessage' => $responseMessage
        ]);
    }

    public function logout(){
        unset($_SESSION['userId']);
        unset($_SESSION['isAdmin']);
        return new RedirectResponse('/');
    }
    
}
