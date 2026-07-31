<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCurrentUser = $request->user() === null || $request->user()->is($this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when(
                $isCurrentUser,
                $this->email
            ),
            'roles' => $this->when(
                $isCurrentUser,
                fn () => $this->getRoleNames()->values()->all()
            ),
            'permissions' => $this->when(
                $isCurrentUser,
                fn () => $this->getAllPermissions()
                    ->pluck('name')
                    ->sort()
                    ->values()
                    ->all()
            ),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
