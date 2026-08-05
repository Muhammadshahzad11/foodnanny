<?php

namespace App\Http\Controllers\Admin;

use App\Services\AppNotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class InboxNotificationController extends AdminController implements HasMiddleware
{
    public function __construct(protected AppNotificationService $appNotificationService)
    {
        parent::__construct();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    public function index(Request $request)
    {
        try {
            $user  = Auth::user();
            $page  = $this->appNotificationService->list($user, (int) $request->get('per_page', 20));
            $items = collect($page->items())->map(fn ($n) => $this->appNotificationService->format($n));

            return response([
                'data' => $items,
                'meta' => [
                    'current_page' => $page->currentPage(),
                    'last_page'    => $page->lastPage(),
                    'per_page'     => $page->perPage(),
                    'total'        => $page->total(),
                ],
                'unread_count' => $this->appNotificationService->unreadCount($user),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function recent()
    {
        try {
            $user = Auth::user();
            $items = $this->appNotificationService->recent($user)->map(
                fn ($n) => $this->appNotificationService->format($n)
            );

            return response([
                'data'         => $items,
                'unread_count' => $this->appNotificationService->unreadCount($user),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function unreadCount()
    {
        try {
            return response([
                'data' => [
                    'unread_count' => $this->appNotificationService->unreadCount(Auth::user()),
                ],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function markRead(string $notification)
    {
        try {
            $item = $this->appNotificationService->markRead(Auth::user(), $notification);
            if (!$item) {
                return response(['status' => false, 'message' => 'Notification not found'], 404);
            }

            return response([
                'data'         => $this->appNotificationService->format($item),
                'unread_count' => $this->appNotificationService->unreadCount(Auth::user()),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function markAllRead()
    {
        try {
            $count = $this->appNotificationService->markAllRead(Auth::user());

            return response([
                'data' => [
                    'marked'       => $count,
                    'unread_count' => 0,
                ],
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(string $notification)
    {
        try {
            $ok = $this->appNotificationService->destroy(Auth::user(), $notification);
            if (!$ok) {
                return response(['status' => false, 'message' => 'Notification not found'], 404);
            }

            return response([
                'status'       => true,
                'unread_count' => $this->appNotificationService->unreadCount(Auth::user()),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
