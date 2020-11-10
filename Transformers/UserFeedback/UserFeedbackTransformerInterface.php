<?php
declare(strict_types=1);

namespace App\Transformers\UserFeedback;

use App\Models\UserFeedback;
use App\Transformers\TransformerInterface;

interface UserFeedbackTransformerInterface extends TransformerInterface
{
    /**
     * @param UserFeedback $feedback
     *
     * @return array
     */
    public function transform(UserFeedback $feedback): array;
}
