<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\BookmarkedContent;

use App\Http\Requests\ApiRequest;

class CreateRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_item_id' => 'required|exists:content_items,id',
            'user_id' => 'exists:users,id',
        ];
    }
}