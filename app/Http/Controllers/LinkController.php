<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Repositories\LinkRepository;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Predis\Client;

class LinkController extends BaseController
{
    protected $redisClient;
    protected $linkRepository;

    public function __construct(Client $client, LinkRepository $linkRepository)
    {
        $this->redisClient = $client;
        $this->linkRepository = $linkRepository;
    }

    public function create(LinkRequest $linkRequest)
    {
        $originalLink = $linkRequest->get('link');
        $user = $linkRequest->get('userId');

        $linkStringCreationFlag = true;
        while ($linkStringCreationFlag) {
            $shortenerLink = Str::random(5);

            $checkShortenerLinkExist = $this->linkRepository->checkExistsByShortenerUrl($shortenerLink);
            if (!$checkShortenerLinkExist) {
                $linkStringCreationFlag = false;
            }
        }

        $link = $this->linkRepository->createLink([
            'original_url' => $originalLink,
            'shortener_url' => $shortenerLink,
            'user_id' => $user,
        ]);

        $this->redisClient->set($shortenerLink, $originalLink);

        return $this->responseSuccess(LinkResource::make($link), 'Link Created Successfully');
    }

    public function mostClickedLinks($size)
    {
        $links = $this->linkRepository->getMostClickedLinks($size);

        return $this->responseSuccess(LinkResource::collection($links), 'Done');
    }

    public function linksByUser($user)
    {
        $links = $this->linkRepository->getByUser($user);

        return $this->responseSuccess(LinkResource::collection($links), 'Done');
    }

    public function load($query)
    {
        $originalLink = $this->redisClient->get($query);
        if ($originalLink == '') {
            return $this->responseError("Url Not Found!");
        }

        ////// This Section Should Move to Background (Event)
            $link = $this->linkRepository->getByShortenerUrl($query);
            if ($link) {
                $link->increment('count');
                $link->save();
            }
        /////////
        return Redirect::to('https://www.' . $originalLink);
    }

    public function search($query)
    {
        $link = $this->linkRepository->getByOriginalUrl($query);
        if (! $link) {
            return $this->responseError('No Related Links Found!');
        }

        return $this->responseSuccess(LinkResource::make($link), 'Done');
    }
}
