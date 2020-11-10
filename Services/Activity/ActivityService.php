<?php
declare(strict_types=1);

namespace App\Services\Activity;

use App\Exceptions\ActivityNotFoundException;
use App\Exceptions\InvalidActivityException;
use App\Factory\ActivityFactory;
use App\Models\Activity;
use App\Models\User;
use App\Services\Activity\Types\BookmarkedActivityInterface;
use App\Services\Activity\Types\CompletedActivityInterface;
use App\Services\Activity\Types\CompletedSessionsActivityInterface;
use App\Services\Activity\Types\WatchedActivityInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;

class ActivityService
{
    /**
     * @var ActivityFactory
     */
    private $activityFactory;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var array
     */
    protected $activities = [
        BookmarkedActivityInterface::TYPE => BookmarkedActivityInterface::class,
        CompletedActivityInterface::TYPE => CompletedActivityInterface::class,
        CompletedSessionsActivityInterface::TYPE => CompletedSessionsActivityInterface::class,
        StartedTopicActivityInterface::TYPE => StartedTopicActivityInterface::class,
        WatchedActivityInterface::TYPE => WatchedActivityInterface::class,
        WrittenReflectionActivityInterface::TYPE => WrittenReflectionActivityInterface::class
    ];

    /**
     * @var array
     */
    protected $associations = [
        BookmarkedActivityInterface::class => [
            'me' => BookmarkedMeActivity::class,
            'company' => BookmarkedCompanyActivity::class,
        ],
    ];

    /**
     * ActivityService constructor.
     * @param ActivityFactory $activityFactory
     * @param Application $application
     */
    public function __construct(ActivityFactory $activityFactory, Application $application)
    {
        $this->activityFactory = $activityFactory;
        $this->application = $application;
    }

    /**
     * @param array $payloadsAr
     * @param Activity $activityModel
     * @return array
     */
    public function preparePayloads(array $payloadsAr, Activity $activityModel): array
    {
        $payloads = [];
        foreach ($payloadsAr as $payload) {
            $payloadModel = $this->resolvePayload($payload['class'], $payload['class_id']);

            if (!$payloadModel instanceof Model) {
                throw new InvalidActivityException($activityModel);
            }

            $payloads[$payload['class']] = $payloadModel;
        }

        return $payloads;
    }

    /**
     * @param string $activityType
     * @param User $user
     * @param array $payloads
     * @return Activity
     * @throws ActivityNotFoundException
     */
    public function createActivity(string $activityType, User $user, array $payloads): Activity
    {
        $activityModel = $this->activityFactory->create($user, $activityType, $payloads);

        $class = $this->getActivityClass($activityModel->type);

        $ar = [
            'me' => 'messageMe',
            'company' => 'messageCompany'
        ];

        foreach ($this->associations[$class] as $activity) {
            $field = $ar[$activity::SECTION];
            $activityClass = new $activity();
            $activityClass->setPayloads($this->preparePayloads($payloads, $activityModel));
            $activityClass->setUser($user);
            $activityModel->{$field} = $activityClass->getText();
        }

        $activityModel->save();

        return $activityModel;
    }

    /**
     * @param $class
     * @param int $id
     *
     * @return Model|null
     */
    protected function resolvePayload($class, int $id): ?Model
    {
        return $class::find($id);
    }

    /**
     * @param string $type
     * @return mixed
     * @throws ActivityNotFoundException
     */
    public function getActivityClass(string $type)
    {
        if (isset($this->activities[$type])) {
            return $this->activities[$type];
        }

        throw new ActivityNotFoundException('Could not resolve activity type');
    }

    /**
     * @return array
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }
}
