<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListener;

use App\Shared\Infrastructure\Request\RequestDtoInterface;
use App\Shared\Infrastructure\Service\ResponseService;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDtoListener
{
    public function __construct(
        private ValidatorInterface $validator,
        private DenormalizerInterface $denormalizer,
        private ResponseService $responseService
    ) {
        $this->denormalizer = new Serializer([
            new UidNormalizer(),
            new DateTimeNormalizer(),
            new BackedEnumNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                null,
                new CamelCaseToSnakeCaseNameConverter(),
                null,
                new PropertyInfoExtractor(
                    typeExtractors: [new PhpDocExtractor(), new ReflectionExtractor()]
                )
            ),
        ]);
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        $request = $event->getRequest();
        $controllerArguments = $event->getArguments();
        $dtoClass = null;
        $dtoIndex = null;
        $dto = null;

        foreach ($controllerArguments as $index => $argument) {
            if ($argument instanceof RequestDtoInterface) {
                $dtoClass = get_class($argument);
                $dtoIndex = $index;
                $dto = $argument;
                break;
            }
        }

        if (!$dtoClass) return;

        // Merge all request sources
        $requestData = array_merge(
            json_decode($request->getContent(), true) ?? [],
            $request->attributes->all(),
            $request->query->all(),
            $request->request->all()
        );

        try {
            // Deserialize into DTO
            $dto = $this->denormalizer->denormalize(
                $requestData,
                $dtoClass,
                'array'
            );
            if ($request->files->all() !== null) {
                if (
                    property_exists($dto, 'file') &&
                    array_key_exists('file', $request->files->all())
                ) {
                    $dto->file = $request->files->all()['file'];
                }
            }
        } catch (NotNormalizableValueException $e) {
        }

        // Validate DTO
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $errorMessages[$propertyPath][] = $error->getMessage();
            }

            $event->setController(fn() => $this->responseService->badRequest(['errors' => $errorMessages]));
            return;
        }

        $arguments = $event->getArguments();
        $arguments[$dtoIndex]  = $dto;
        $event->setArguments($arguments);
    }
}
