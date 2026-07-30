<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Services\OrderService;
use App\Exports\EmployeeExport;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\ChangeImageRequest;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\UserChangePasswordRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Resources\EmployeeDetailsResource;
use App\Http\Resources\SimpleAdminUserResource;

class EmployeeController extends AdminController implements HasMiddleware
{
    private EmployeeService $employeeService;
    private OrderService $orderService;

    public function __construct(EmployeeService $employeeService, OrderService $orderService)
    {
        parent::__construct();
        $this->employeeService = $employeeService;
        $this->orderService    = $orderService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:employees', only: ['index', 'export']),
            new Middleware('permission:employees_create', only: ['store']),
            new Middleware('permission:employees_edit', only: ['update']),
            new Middleware('permission:employees_delete', only: ['destroy']),
            new Middleware('permission:employees_show', only: ['show', 'changePassword', 'changeImage', 'myOrder']),
            new Middleware('permission:collections|cashouts', only: ['allEmployeeAndAdmin']),
            new Middleware('permission:collections_create', only: ['employeeAndAdminShow'])
        ];
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return EmployeeResource::collection($this->employeeService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(EmployeeRequest $request): \Illuminate\Http\Response|EmployeeResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new EmployeeResource($this->employeeService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(EmployeeRequest $request, User $employee): \Illuminate\Http\Response|EmployeeResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new EmployeeResource($this->employeeService->update($request, $employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(User $employee): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->employeeService->destroy($employee);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(User $employee): \Illuminate\Foundation\Application|\Illuminate\Http\Response|EmployeeDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new EmployeeDetailsResource($this->employeeService->show($employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new EmployeeExport($this->employeeService, $request), 'Employee.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changePassword(UserChangePasswordRequest $request, User $employee): \Illuminate\Http\Response|EmployeeResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new EmployeeResource($this->employeeService->changePassword($request, $employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, User $employee): \Illuminate\Http\Response|EmployeeResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if(env('DEMO')) {
                return response(['status' => false, 'message' => trans('all.message.action_is_disabled_in_demo_mode')], 422);
            }else {
               return new EmployeeResource($this->employeeService->changeImage($request, $employee));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function myOrder(PaginateRequest $request, User $employee): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderResource::collection($this->orderService->userOrder($request, $employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function allEmployeeAndAdmin(Request $request): \Illuminate\Http\Response|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleAdminUserResource::collection($this->employeeService->allEmployeeAndAdmin($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function employeeAndAdminShow(User $employee): \Illuminate\Foundation\Application|\Illuminate\Http\Response|EmployeeDetailsResource|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new EmployeeDetailsResource($this->employeeService->employeeAndAdminShow($employee));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
