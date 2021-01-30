<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Util\Validation\ValidationUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BaseController extends AbstractController
{
    public const CONTENT_TYPE = 'application/json';
    private const RESPONSE_FORMAT = 'json';

    /**
     * @var ValidationUtil
     */
    private $validation;

    public function __construct(ValidationUtil $validation)
    {
        $this->validation = $validation;
    }

    /**
     * @param string $contentType
     */
    protected function validateContentType(string $contentType): void
    {
        if (self::CONTENT_TYPE !== $contentType) {
            throw new ValidationException(
                'Invalid content type header.',
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }
    }


    /**
     * @param $data
     * @param $model
     *
     * @return object
     */
    protected function validateRequest($data, $model): object
    {
        return $this->validation->validate($data,$model);
    }
}