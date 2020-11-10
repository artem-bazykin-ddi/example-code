<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function wantsJson(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    protected function validationData(): array
    {
        return $this->json()->all();
    }

    /**
     * Generate error messages to json response
     * if method wantsJson set to TRUE
     *
     * @param Validator $validator
     *
     * @return JsonResponse
     */
    protected function failedValidation(Validator $validator): JsonResponse
    {
        if ($this->wantsJson()) {
            $responseErrors = [];
            $messages = $validator->errors()->messages();

            foreach ($messages as $key => $message) {

                $responseErrors[$key] = $message;
            }

            throw new HttpResponseException(
                new JsonResponse(['errors' => $responseErrors], 422)
            );
        }

        return new JsonResponse(['message' => 'Something went wrong'], 400);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
