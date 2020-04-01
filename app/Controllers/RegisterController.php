<?php

namespace App\Controllers;

use Respect\Validation\Validator as v;
use Laminas\Diactoros\Response\RedirectResponse;
use App\Models\User;

class RegisterController extends BaseController {

    protected $title = "Register";

    private function createFormValidator($parsedData){
        $validator = v::key('email', v::email())
            ->key('password', v::stringType()->notEmpty()->noWhitespace()
            ->length(6,20))
            ->key('cpassword', v::equals($parsedData['password']))
            ->key('countryId', v::intType())
            ->key('firstName', v::stringType()->notEmpty())
            ->key('lastName', v::stringType()->notEmpty())
            ->key('address', v::stringType()->notEmpty())
            ->key('phoneNumber', v::stringType()->notEmpty())
            ->key('username', v::stringType()->notEmpty()->noWhitespace())
            ->key('gender', v::stringType()->notEmpty());
        return $validator;
    }

    public function createUser($parsedData){
        $user = new User();

        $user->email = $parsedData['email'];
        $hashedPass = password_hash($parsedData['password'], PASSWORD_DEFAULT);
        $user->password = $hashedPass;
        $user->firstName = $parsedData['firstName'];
        $user->lastName = $parsedData['lastName'];
        $user->countryId = $parsedData['countryId'];
        $user->address = $parsedData['address'];
        $user->gender = $parsedData['gender'];
        $user->phoneNumber = $parsedData['phoneNumber'];
        $user->username = $parsedData['username'];
        $user->isAdmin = false;

        return $user;
    }

    public function getRegister(){
        return $this->renderHTML("register.twig");
    }

    public function postRegister($request){
        $method = $request->getMethod();
        $responseMessage = null;
        $errorMessages = null;

        if($method == "POST"){
            $parsedData = $request->getParsedBody();

            $userValidator = $this->createFormValidator($parsedData);

            try{
                $parsedData['countryId'] = (int)$parsedData['countryId'];
                $userValidator->assert($parsedData);
                
                $userInTable = User::where('email', $parsedData['email'])->first();
                if($userInTable){
                    throw new \Exception('User is already registered');
                }
                $userInTable = User::where('username', $parsedData['username'])->first();
                if($userInTable){
                    throw new \Exception('Username is already registered');
                }

                $user = $this->createUser($parsedData);

                $user->save();
                $responseMessage = 'Te has registrado en tickera.';

            } catch (\Exception $e) {
                if($e->getMessage() === 'User is already registered'){
                    $errorMessages = ['El correo ya esta registrado.'];
                }
                else if($e->getMessage() === 'Username is already registered'){
                    $errorMessages = ['El nombre de usuario ya esta registrado.'];
                }
                else{
                    $errorMessages = $e->findMessages([
                        'email' => 'Ingresa un email valido.',
                        'intType' => 'Ingresa una cedula valida',
                        'noWhitespace' => 'No uses espacios en blanco en tu nombre de usuario ni en tu contraseña.',
                        'notEmpty' => 'Completa toda la información requerida.',
                        'length' => 'La contraseña debe estar entre 6-20 caractéres',
                        'equals' => 'Las contraseñas son diferentes.'
                    ]);
                }
                //var_dump($e);
            }
        }
        return $this->renderHTML("register.twig",[
            'responseMessage' => $responseMessage,
            'errorMessages' => $errorMessages
        ]);
    }
    
}
