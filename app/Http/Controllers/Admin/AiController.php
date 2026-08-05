<?php

namespace App\Http\Controllers\Admin;



use App\Http\Resources\AiChatResource;
use App\Models\AiAgent;
use App\Models\AiChatHistory;
use App\Models\Restaurant;
use App\Services\AiChatHistoryService;
use App\Services\AiUsageService;
use App\Traits\DefaultAccessModelTrait;
use Dipokhalder\Settings\Facades\Settings;
use App\Http\Requests\AiRequest;
use App\Services\AiService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class AiController extends AdminController implements HasMiddleware
{
    use DefaultAccessModelTrait;

    public string $agent;
    public AiService $aiService;
    public AiUsageService $aiUsageService;
    public AiChatHistoryService $aiChatHistoryService;

    public function __construct(AiService $aiService, AiUsageService $aiUsageService, AiChatHistoryService $aiChatHistoryService)
    {
        parent::__construct();
        $this->aiService            = $aiService;
        $this->aiUsageService       = $aiUsageService;
        $this->aiChatHistoryService = $aiChatHistoryService;
        $defaultAiAgent             = Settings::group('site')->get('site_default_ai_agent');
        if ($defaultAiAgent > 0) {
            $agent = AiAgent::find($defaultAiAgent);
            if ($agent) {
                $this->agent = $agent->slug;
            }
        }
    }

    public static function middleware(): array
    {
        return [];
    }

    public function name(AiRequest $aiRequest): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if (!$this->aiUsageService->textUsage($restaurant)) {
                return response(['status' => false, 'message' => trans('all.message.your_data_generation_limit_is_over')], 422);
            }

            if ($this->aiService->agent($this->agent)->status()) {
                return response(['status' => true, 'data' => $this->aiService->agent($this->agent)->name($aiRequest)], 200);
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function description(AiRequest $aiRequest): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if (!$this->aiUsageService->textUsage($restaurant)) {
                return response(['status' => false, 'message' => trans('all.message.your_data_generation_limit_is_over')], 422);
            }

            if ($this->aiService->agent($this->agent)->status()) {
                return response(['status' => true, 'data' => $this->aiService->agent($this->agent)->description($aiRequest)], 200);
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chat(AiRequest $aiRequest): \Illuminate\Http\Response|AiChatResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $restaurant = Restaurant::find($this->restaurant());
            if (!$this->aiUsageService->textUsage($restaurant)) {
                return response(['status' => false, 'message' => trans('all.message.your_data_generation_limit_is_over')], 422);
            }

            if ($this->aiService->agent($this->agent)->status()) {
                return new AiChatResource($this->aiChatHistoryService->store($aiRequest, $restaurant));
            }
            return response(['status' => false, 'message' => trans('all.message.agent_is_not_active')], 422);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chatResponse(AiChatHistory $aiChatHistory, AiRequest $aiRequest): \Illuminate\Http\Response|AiChatResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new AiChatResource($this->aiChatHistoryService->update($aiChatHistory, $this->aiService->agent($this->agent)->message($aiRequest)));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function chatHistory(): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $restaurant  = Restaurant::find($this->restaurant());
            return AiChatResource::collection($this->aiChatHistoryService->list($restaurant));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deleteChatHistory(Restaurant $restaurant): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $chatHistory = AiChatHistory::where(['restaurant_id' => $restaurant->id, 'user_id' => Auth::user()->id, 'ai_agent_id' => Settings::group('site')->get('site_default_ai_agent')])->get();
            $chatHistory->each(function ($chat) {
                $chat->delete();
            });
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function status(): \Illuminate\Http\Response|bool|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response(['status' => false, 'data' => $this->aiUsageService->checking()], 200);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}

