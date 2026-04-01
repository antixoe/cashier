<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description = null,
        string $modelType = null,
        int $modelId = null,
        array $oldValues = null,
        array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin(): void
    {
        self::log(
            action: 'login',
            description: 'User logged in'
        );
    }

    /**
     * Log logout activity
     */
    public static function logLogout(): void
    {
        self::log(
            action: 'logout',
            description: 'User logged out'
        );
    }

    /**
     * Log product added to cart
     */
    public static function logAddToCart($product, $quantity): void
    {
        self::log(
            action: 'add_to_cart',
            description: "Added {$product->name} (Qty: {$quantity}) to cart",
            modelType: 'Product',
            modelId: $product->id,
            newValues: [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
            ]
        );
    }

    /**
     * Log product removed from cart
     */
    public static function logRemoveFromCart($product, $quantity): void
    {
        self::log(
            action: 'remove_from_cart',
            description: "Removed {$product->name} (Qty: {$quantity}) from cart",
            modelType: 'Product',
            modelId: $product->id,
            oldValues: [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
            ]
        );
    }

    /**
     * Log checkout/sale
     */
    public static function logCheckout($sale, $total, $itemCount): void
    {
        self::log(
            action: 'checkout',
            description: "Completed sale with {$itemCount} items totaling Rp " . number_format($total, 2),
            modelType: 'Sale',
            modelId: $sale->id,
            newValues: [
                'sale_id' => $sale->id,
                'total' => $total,
                'item_count' => $itemCount,
            ]
        );
    }

    /**
     * Log user creation
     */
    public static function logUserCreate($user, array $userData): void
    {
        self::log(
            action: 'create',
            description: "Created new user: {$user->name} ({$user->email})",
            modelType: 'User',
            modelId: $user->id,
            newValues: $userData
        );
    }

    /**
     * Log user update
     */
    public static function logUserUpdate($user, array $oldData, array $newData): void
    {
        self::log(
            action: 'update',
            description: "Updated user: {$user->name}",
            modelType: 'User',
            modelId: $user->id,
            oldValues: $oldData,
            newValues: $newData
        );
    }

    /**
     * Log user deletion
     */
    public static function logUserDelete($user): void
    {
        self::log(
            action: 'delete',
            description: "Deleted user: {$user->name} ({$user->email})",
            modelType: 'User',
            modelId: $user->id,
            oldValues: [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }

    /**
     * Log role creation
     */
    public static function logRoleCreate($role): void
    {
        self::log(
            action: 'create',
            description: "Created new role: {$role->name}",
            modelType: 'Role',
            modelId: $role->id,
            newValues: ['name' => $role->name]
        );
    }

    /**
     * Log role update
     */
    public static function logRoleUpdate($role, array $oldData, array $newData): void
    {
        self::log(
            action: 'update',
            description: "Updated role: {$role->name}",
            modelType: 'Role',
            modelId: $role->id,
            oldValues: $oldData,
            newValues: $newData
        );
    }

    /**
     * Log role deletion
     */
    public static function logRoleDelete($role): void
    {
        self::log(
            action: 'delete',
            description: "Deleted role: {$role->name}",
            modelType: 'Role',
            modelId: $role->id,
            oldValues: ['name' => $role->name]
        );
    }

    /**
     * Get all activity logs with pagination
     */
    public static function getActivityLogs($perPage = 50)
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get activity logs filtered by user
     */
    public static function getUserActivityLogs($userId, $perPage = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get activity logs filtered by action
     */
    public static function getActivityLogsByAction($action, $perPage = 50)
    {
        return ActivityLog::where('action', $action)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get activity logs for a specific date range
     */
    public static function getActivityLogsByDateRange($startDate, $endDate, $perPage = 50)
    {
        return ActivityLog::whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
