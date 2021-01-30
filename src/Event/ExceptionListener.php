<?php


namespace App\Event;

use Exception;
use App\Exception\ApplicationException;
use App\Service\Log\ExceptionLogService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;


class ExceptionListener
{
    private $logger;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ExceptionLogService $logger,
        TranslatorInterface $translator
    )
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof ApplicationException) {
            $response = $this->handleKnownExceptions($event->getThrowable());
        } else {
            $response = $this->handleUnknownExceptions($event->getThrowable());
        }
        $event->setResponse($response);
    }

    private function handleKnownExceptions(Exception $exception): Response
    {
        $message = $exception->getMessage();
        if (Response::HTTP_BAD_REQUEST === $exception->getStatusCode()) {
            $message = $this->translator->trans($exception->getMessage());
        } else {
            $exceptionInfo = [
                "line" => $exception->getLine(),
                "file" => $exception->getFile(),
                "message" => $exception->getMessage(),
            ];
            $this->logger->add('error', json_encode($exceptionInfo));
        }

        return new Response(
            json_encode(["error" => $message]),
            $exception->getStatusCode(),
            ['Content-Type' => 'application/json',]
        );
    }

    private function handleUnknownExceptions($exception): Response
    {
        $exceptionInfo = [
            "line" => $exception->getLine(),
            "file" => $exception->getFile(),
            "message" => $exception->getMessage(),
        ];
        $this->logger->add('error', json_encode($exceptionInfo));

        return new Response(
            json_encode(["error" => $this->translator->trans('messages.critical.public')]),
            Response::HTTP_BAD_REQUEST,
            ['Content-Type' => 'application/json',]
        );
    }
}