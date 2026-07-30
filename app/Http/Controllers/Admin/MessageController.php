<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\MessageUserResource;
use App\Models\Order;
use Exception;
use App\Services\MessageService;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class MessageController extends AdminController implements HasMiddleware
{
    public MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        parent::__construct();
        $this->messageService = $messageService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:messages', only: ['index', 'show', 'store'])
        ];
    }

    public function index(): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return MessageUserResource::collection($this->messageService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Order $order): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return MessageResource::collection($this->messageService->show($order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(MessageRequest $request): \Illuminate\Foundation\Application|\Illuminate\Http\Response|MessageResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MessageResource($this->messageService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
