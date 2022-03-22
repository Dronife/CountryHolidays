<?php

namespace App\Factory\Error;

use App\Model\Error\ErrorResponseModel;
use App\Model\Error\BaseErrorModel;
use Symfony\Component\Form\FormInterface;

/**
 * Class ErrorResponseFactory
 * @package App\Factory\API\Error
 */
class ErrorResponseFactory
{
    /**
     * @var BaseErrorModelFactory
     */
    private BaseErrorModelFactory $baseErrorFactory;

    /**
     * @var FormErrorModelFactory
     */
    private FormErrorModelFactory $formErrorFactory;

    /**
     * ErrorResponseFactory constructor.
     * @param BaseErrorModelFactory $baseErrorFactory
     * @param FormErrorModelFactory $formErrorFactory
     */
    public function __construct(BaseErrorModelFactory $baseErrorFactory, FormErrorModelFactory $formErrorFactory)
    {
        $this->baseErrorFactory = $baseErrorFactory;
        $this->formErrorFactory = $formErrorFactory;
    }

    /**
     * @param FormInterface $form
     * @return ErrorResponseModel
     */
    public function createWithFormError(FormInterface $form): ErrorResponseModel
    {
        $errorResponse = new ErrorResponseModel();
        if ($form->getErrors()->count() > 0) {
            $errorResponse->addFormError(
                $this->formErrorFactory->create(
                    $form->getName(), $form->getErrors()->current()->getMessage()
                )
            );
        }
        foreach ($form->all() as $child) {
            $this->checkChildValidity($errorResponse, $child);
        }

        return $errorResponse;
    }

    /**
     * @param string $message
     * @param string $code
     * @return ErrorResponseModel
     */
    public function createWithBaseError(
        string $message,
        string $code = BaseErrorModel::CODE_NOT_APPLICABLE
    ): ErrorResponseModel {
        return (new ErrorResponseModel())
            ->setError($this->baseErrorFactory->create($message, $code));
    }

    /**
     * @param ErrorResponseModel $errorResponseModel
     * @param FormInterface $childForm
     * @return ErrorResponseModel
     */
    private function checkChildValidity(
        ErrorResponseModel $errorResponseModel,
        FormInterface $childForm
    ): ErrorResponseModel {
        if ($childForm->isSubmitted() && !$childForm->isValid()) {
            foreach ($childForm->all() as $child) {
                $this->checkChildValidity($errorResponseModel, $child);
            }

            if ($childForm->getErrors()->count() > 0) {
                $errorResponseModel->addFormError(
                    $this->formErrorFactory->create(
                        $childForm->getName(), $childForm->getErrors()->current()->getMessage()
                    )
                );
            }
        }

        return $errorResponseModel;
    }
}
