<?php

namespace App\Services;

use Exception;
use App\Enums\Ask;
use App\Models\User;
use App\Enums\Role as EnumRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Traits\DefaultAccessModelTrait;


class EmployeeService
{
    use DefaultAccessModelTrait;

    public object $user;
    public array $roleFilter = ['role_id'];
    public array $userFilter = ['name', 'email', 'phone', 'status'];
    public array $blockRoles = [EnumRole::ADMIN, EnumRole::RESTAURANT_OWNER, EnumRole::DELIVERY_BOY, EnumRole::CUSTOMER];
    public array $restaurantEmployeeRoles = [
        EnumRole::WAITER,
        EnumRole::CHEF,
        EnumRole::CASHIER,
        EnumRole::MANAGER,
    ];


    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';
            $restaurantId = $this->scopedRestaurantId();

            return User::with('media', 'addresses', 'roles')->where(
                function ($query) use ($requests, $restaurantId) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('id', '!=', EnumRole::ADMIN);
                        $query->where('id', '!=', EnumRole::RESTAURANT_OWNER);
                        $query->where('id', '!=', EnumRole::DELIVERY_BOY);
                        $query->where('id', '!=', EnumRole::CUSTOMER);
                    });

                    if ($restaurantId > 0) {
                        $query->where('restaurant_id', $restaurantId);
                    }

                    foreach ($requests as $key => $request) {
                        if (in_array($key, $this->roleFilter)) {
                            $query->whereHas('roles', function ($query) use ($request, $key) {
                                $query->where('id', '=', $request);
                            });
                        }
                        if (in_array($key, $this->userFilter)) {
                            if ($key == 'phone') {
                                $query->whereRaw("CONCAT(country_code, phone) LIKE ?", ["%{$request}%"]);
                            } else {
                                $query->where($key, 'like', '%' . $request . '%');
                            }
                        }
                    }
                }
            )->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(EmployeeRequest $request): object
    {
        try {
            $this->assertAssignableRole((int) $request->role_id);
            $restaurantId = $this->resolveRestaurantIdForWrite($request->restaurant_id);

            DB::transaction(function () use ($request, $restaurantId) {
                $this->user = User::create([
                    'name'                 => $request->name,
                    'email'                => $request->email,
                    'phone'                => $request->phone,
                    'username'             => $this->username($request->email),
                    'password'             => bcrypt($request->password),
                    'restaurant_id'        => $restaurantId,
                    'status'               => $request->status,
                    'email_verified_at'    => now(),
                    'country_code'         => $request->country_code,
                    'is_guest'             => Ask::NO,
                    'terms_and_conditions' => Ask::YES
                ]);
                $this->user->assignRole($request->role_id);
            });
            app(RealtimePublisher::class)->employee($this->user->fresh(), 'created');

            return $this->user;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(EmployeeRequest $request, User $employee): object
    {
        try {
            $this->assertCanManage($employee);
            $this->assertAssignableRole((int) $request->role_id);
            $restaurantId = $this->resolveRestaurantIdForWrite($request->restaurant_id);

            DB::transaction(function () use ($employee, $request, $restaurantId) {
                $this->user                = $employee;
                $this->user->name          = $request->name;
                $this->user->email         = $request->email;
                $this->user->phone         = $request->phone;
                $this->user->restaurant_id = $restaurantId;
                $this->user->status        = $request->status;
                $this->user->country_code  = $request->country_code;
                if ($request->password) {
                    $this->user->password = Hash::make($request->password);
                }
                $this->user->save();
                $this->user->syncRoles($request->role_id);
            });
            app(RealtimePublisher::class)->employee($this->user->fresh(), 'updated');

            return $this->user;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(User $employee): User
    {
        try {
            $this->assertCanManage($employee);
            return $employee->load('roles');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(User $employee): void
    {
        try {
            $this->assertCanManage($employee);
            if ($employee->hasRole(optional($employee->roles[0])->id)) {
                DB::transaction(function () use ($employee) {
                    $employee->addresses()->delete();
                    $employee->delete();
                });
            } else {
                throw new Exception(trans('all.message.permission_denied'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            DB::rollBack();
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function username($email): string
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @throws Exception
     */
    public function changePassword(UserChangePasswordRequest $request, User $employee): User
    {
        try {
            $this->assertCanManage($employee);
            $employee->password = Hash::make($request->password);
            $employee->save();
            return $employee;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function changeImage(ChangeImageRequest $request, User $employee): User
    {
        try {
            $this->assertCanManage($employee);
            if ($request->image) {
                $employee->clearMediaCollection('profile');
                $employee->addMediaFromRequest('image')->toMediaCollection('profile');
            }
            return $employee;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function allEmployeeAndAdmin($request): \Illuminate\Database\Eloquent\Collection
    {
        $requests    = $request->all();
        $orderColumn = $request->get('order_column') ?? 'id';
        $orderType   = $request->get('order_type') ?? 'desc';

        return User::with('roles')->where(
            function ($query) use ($requests) {
                if (isset($requests['except_me']) && $requests['except_me'] > 0) {
                    $query->where('id', '!=', $requests['except_me']);
                }
                $query->whereHas('roles', function ($query) {
                    $query->where('id', '!=', EnumRole::RESTAURANT_OWNER);
                    $query->where('id', '!=', EnumRole::DELIVERY_BOY);
                    $query->where('id', '!=', EnumRole::CUSTOMER);
                });
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->roleFilter)) {
                        $query->whereHas('roles', function ($query) use ($request, $key) {
                            $query->where('id', '=', $request);
                        });
                    }

                    if (in_array($key, $this->userFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            }
        )->orderBy($orderColumn, $orderType)->get();
    }

    /**
     * @throws Exception
     */
    public function employeeAndAdminShow(User $user): User
    {
        try {
            return $user;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    protected function actorRoleId(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 0;
        }

        return (int) optional($user->roles->first())->id;
    }

    protected function isRestaurantOwnerActor(): bool
    {
        return $this->actorRoleId() === EnumRole::RESTAURANT_OWNER;
    }

    protected function scopedRestaurantId(): int
    {
        $restaurantId = (int) $this->restaurant();
        if ($restaurantId > 0) {
            return $restaurantId;
        }

        if ($this->isRestaurantOwnerActor()) {
            return (int) Auth::user()->restaurant_id;
        }

        return 0;
    }

    /**
     * @throws Exception
     */
    protected function resolveRestaurantIdForWrite(mixed $requestedRestaurantId): int
    {
        $scopedRestaurantId = $this->scopedRestaurantId();

        if ($this->isRestaurantOwnerActor()) {
            if ($scopedRestaurantId <= 0) {
                throw new Exception(trans('all.message.restaurant_required_for_employee'), 422);
            }
            return $scopedRestaurantId;
        }

        if ($scopedRestaurantId > 0) {
            return $scopedRestaurantId;
        }

        return (int) ($requestedRestaurantId ?? 0);
    }

    /**
     * @throws Exception
     */
    protected function assertAssignableRole(int $roleId): void
    {
        if (in_array($roleId, $this->blockRoles, true)) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }

        if ($this->isRestaurantOwnerActor() && !in_array($roleId, $this->restaurantEmployeeRoles, true)) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }

    /**
     * @throws Exception
     */
    protected function assertCanManage(User $employee): void
    {
        $employee->loadMissing('roles');

        if (in_array((int) optional($employee->roles->first())->id, $this->blockRoles, true)) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }

        $scopedRestaurantId = $this->scopedRestaurantId();
        if ($scopedRestaurantId > 0 && (int) $employee->restaurant_id !== $scopedRestaurantId) {
            throw new Exception(trans('all.message.permission_denied'), 422);
        }
    }
}
