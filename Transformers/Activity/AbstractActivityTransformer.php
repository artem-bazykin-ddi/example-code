<?php

namespace App\Transformers\Activity;

use App\Models\Activity;
use App\Services\Activity\ActivityService;
use App\Transformers\AbstractTransformer;

/**
 * Class AbstractActivityTransformer
 * @package App\Transformers\Activity
 */
class AbstractActivityTransformer extends AbstractTransformer
{

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * ActivityMeTransformer constructor.
     * @param ActivityService $activityService
     */
    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * @param Activity $activity
     * @return array
     */
    protected function getMeta(Activity $activity): array
    {
        $payloads = json_decode($activity->payloads, true, 512, JSON_THROW_ON_ERROR);

        if (count($payloads) > 0) {
            $payloads = $payloads[0];
        }

        if ($payloads['class'] === ContentItem::class) {
            $meta = [
                'contentItemId' => $payloads['class_id']
            ];
        } else {
            $meta = [];
        }

        return $meta;
    }
}