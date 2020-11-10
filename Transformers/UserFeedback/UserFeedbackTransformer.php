<?php
declare(strict_types=1);

namespace App\Transformers\UserFeedback;

use App\Models\UserFeedback;
use League\Fractal\TransformerAbstract;

class UserFeedbackTransformer extends TransformerAbstract implements UserFeedbackTransformerInterface
{
    /**
     * A Fractal transformer.
     *
     * @param UserFeedback $userFeedback
     *
     * @return array
     */
    public function transform(UserFeedback $userFeedback): array
    {
        return [
            'id' => $userFeedback->getKey(),
            'user_id' => $userFeedback->user_id,
            'content_item_id' => $userFeedback->content_item_id,
            'reaction' => $userFeedback->reaction,
            'response' => $userFeedback->response,
        ];
    }
}
