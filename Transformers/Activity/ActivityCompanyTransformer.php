<?php
declare(strict_types=1);

namespace App\Transformers\Activity;

use App\Models\Activity;

class ActivityCompanyTransformer extends AbstractActivityTransformer implements ActivityTransformerInterface
{

    /**
     * @param Activity $activity
     * @return array
     */
    public function transform(Activity $activity): array
    {
        $meta = $this->getMeta($activity);

        return [
            'id' => $activity->id,
            'image' => $this->avatar($activity->user->avatar),
            'first_name' => $activity->user->first_name,
            'last_name' => $activity->user->last_name,
            'event' => $activity->messageCompany,
            'date' => $this->date($activity->created_at),
            'meta' => $meta
        ];
    }
}