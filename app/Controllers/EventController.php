<?php

namespace App\Controllers;

use App\Models\Event;
// use Laminas\Diactoros\Response\RedirectResponse;
use Respect\Validation\Validator as v;

class EventController extends BaseController
{
    protected $title = 'Agregar evento - Tickera.com';

    private function createFormValidator($parsedData)
    {
        $validator = v::key('eventName', v::email())
            ->key('eventDate', v::stringType()->notEmpty())
            ->key('vipsAvailable', v::intType())
            ->key('platinumsAvailable', v::intType())
            ->key('highsAvailable', v::intType())
            ->key('mediumAvailable', v::intType());
        return $validator;
    }

    public function createEvent($parsedData)
    {
        $event = new Event();

        $format = 'Y-m-d H:i:s';
        $eventDate = date_create_from_format($format, $parsedData['eventDate']);

        $event->eventName = $parsedData['eventName'];
        $event->eventDate = $eventDate;
        $event->vipsAvailable = $parsedData['vipsAvailable'];
        $event->platinumsAvailable = $parsedData['platinumsAvailable'];
        $event->highsAvailable = $parsedData['highsAvailable'];
        $event->mediumAvailable = $parsedData['mediumAvailable'];

        return $event;
    }

    public function getAddEventForm()
    {
        return $this->renderHTML("addEventForm.twig");
    }

    public function postAddEventForm($request)
    {
        $method = $request->getMethod();
        $responseMessage = null;
        $errorMessages = null;

        if ($method == "POST") {
            $parsedData = $request->getParsedBody();
            try {
                $event = $this->createEvent($parsedData);
                $event->save();
                $responseMessage = 'Has registrado el evento.';
            } catch (\Exception $e) {
                $errorMessages = $e->getMessage();
            }
        }
        return $this->renderHTML("addEventForm.twig", [
            'responseMessage' => $responseMessage,
            'errorMessages' => $errorMessages,
        ]);
    }

}
