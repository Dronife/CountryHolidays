<?php

namespace App\Model\Error;

use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * Class ErrorResponseModel
 * @package App\Model\API\Error
 */
class ErrorResponseModel
{
    /**
     * @var BaseErrorModel|null
     */
    private ?BaseErrorModel $error = null;

    /**
     * @var FormErrorModel[]|ArrayCollection
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=FormErrorModel::class)))
     */
    private $formErrors;

    /**
     * ErrorResponseModel constructor.
     */
    public function __construct()
    {
        $this->formErrors = new ArrayCollection();
    }

    /**
     * @return BaseErrorModel|null
     */
    public function getError(): ?BaseErrorModel
    {
        return $this->error;
    }

    /**
     * @param BaseErrorModel|null $error
     * @return ErrorResponseModel
     */
    public function setError(?BaseErrorModel $error): ErrorResponseModel
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormErrors(): array
    {
        return $this->formErrors->toArray();
    }

    /**
     * @param ArrayCollection $formErrors
     * @return $this
     */
    public function setFormErrors(ArrayCollection $formErrors): self
    {
        $this->formErrors = $formErrors;

        return $this;
    }

    /**
     * @param FormErrorModel $formError
     * @return $this
     */
    public function addFormError(FormErrorModel $formError): self
    {
        if (!$this->formErrors->contains($formError)) {
            $this->formErrors->add($formError);
        }

        return $this;
    }
}
