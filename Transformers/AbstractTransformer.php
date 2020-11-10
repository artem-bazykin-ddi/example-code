<?php
declare(strict_types=1);

namespace App\Transformers;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Storage;

abstract class AbstractTransformer extends TransformerAbstract
{
    /**
     * @param Model $model
     * @param array $fields
     *
     * @return array
     */
    protected function getBodyFields(Model $model, array $fields): array
    {
        $values = [];

        foreach ($fields as $field) {
            $values[$field] = $model->{$field};
        }

        return $values;
    }

    /**
     * @param string|DateTime $dateTime
     *
     * @return string
     * @throws
     */
    protected function date($dateTime): string
    {
        if (!$dateTime instanceof DateTime) {
            $dateTime = new DateTime($dateTime);
        }

        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @param $image
     * @param string $disk
     *
     * @return string|null
     */
    protected function image(?string $image, $disk = 'public'): ?string
    {
        return $image ? Storage::disk($disk)->url($image) : null;
    }

    /**
     * @param string|null $image
     *
     * @return string|null
     */
    protected function avatar(?string $image): ?string
    {
        return $this->image($image, 'avatars');
    }
}
