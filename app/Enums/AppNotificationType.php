<?php

namespace App\Enums;

interface AppNotificationType
{
    public const ORDER_CREATED      = 'order.created';
    public const ORDER_UPDATED      = 'order.updated';
    public const KITCHEN_ACCEPTED   = 'kitchen.accepted';
    public const KITCHEN_PREPARING  = 'kitchen.preparing';
    public const KITCHEN_READY      = 'kitchen.ready';
    public const KITCHEN_SERVED     = 'kitchen.served';
    public const ORDER_COMPLETED    = 'order.completed';
    public const ORDER_CANCELLED    = 'order.cancelled';
    public const ORDER_REJECTED     = 'order.rejected';
    public const TABLE_OCCUPIED     = 'table.occupied';
    public const TABLE_AVAILABLE    = 'table.available';
    public const TABLE_UPDATED      = 'table.updated';
    public const EMPLOYEE_CREATED   = 'employee.created';
    public const EMPLOYEE_UPDATED   = 'employee.updated';
    public const SYSTEM             = 'system';
}
