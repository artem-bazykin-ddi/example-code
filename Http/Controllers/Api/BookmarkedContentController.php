<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\BookmarkedContent\CreateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\UserFeedbackManager;
use App\Models\ContentItem;
use App\Models\User;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\UserFeedback\UserFeedbackTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Swagger\Annotations as SWG;

class BookmarkedContentController extends Controller
{
    /**
     *
     * @SWG\Get(
     *     path="/bookmarked-content",
     *     summary="Get bookmarked Content Items",
     *     security={{"apiToken": {}}},
     *     tags={"Bookmarked Content"},
     *     @SWG\Parameter(
     *         name="focus_id",
     *         description="Filter by Focus ID",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="user_id",
     *         description="Filter by User ID",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of Content Items",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/ContentItem")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ContentItemTransformerInterface $transformer
     * @param ListResponseInterface $response
     * @return JsonResponse
     */
    public function index(ListRequest $request, ContentItemTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        /** @var User $user */
        $user = User::find($request->get('user_id')) ?? Auth::user();

        $query = ContentItem::bookmarked($user);

        if ($request->get('focus_id')) {
            $response->setWhere(['focus_id', $request->get('focus_id')]);
        }

        return $response
            ->setBuilder($query)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($transformer)
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/bookmarked-content",
     *     summary="Get bookmarked Content Items",
     *     security={{"apiToken": {}}},
     *     tags={"Bookmarked Content"},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Feedback Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="content_item_id",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="user_id",
     *                 type="integer",
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param CreateRequest $request
     * @param UserFeedbackManager $userFeedbackManager
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request, UserFeedbackManager $userFeedbackManager): JsonResponse
    {
        $contentItem = ContentItem::findOrFail($request->get('content_item_id'));
        $user = User::find($request->get('user_id'));
        $reaction = true;

        $feedback = $userFeedbackManager->createFeedback($contentItem, $reaction, null, $user);

        return new JsonResponse(fractal($feedback, new UserFeedbackTransformer()));
    }
}
