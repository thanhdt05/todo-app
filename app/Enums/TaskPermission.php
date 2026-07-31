<?php

namespace App\Enums;

enum TaskPermission: string
{
    case VIEW_ALL = 'tasks.view_all';
    case VIEW_OWN = 'tasks.view_own';

    case VIEW_TRASHED_ALL = 'tasks.view_trashed_all';
    case VIEW_TRASHED_OWN = 'tasks.view_trashed_own';

    case CREATE = 'tasks.create';

    case UPDATE_ALL = 'tasks.update_all';
    case UPDATE_OWN = 'tasks.update_own';

    case COMPLETE_ALL = 'tasks.complete_all';
    case COMPLETE_OWN = 'tasks.complete_own';

    case DELETE_ALL = 'tasks.delete_all';
    case DELETE_OWN = 'tasks.delete_own';

    case RESTORE_ALL = 'tasks.restore_all';
    case RESTORE_OWN = 'tasks.restore_own';

    case FORCE_DELETE_ALL = 'tasks.force_delete_all';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $permission): string => $permission->value,
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::VIEW_ALL => 'Xem tất cả công việc',
            self::VIEW_OWN => 'Xem công việc của mình',

            self::VIEW_TRASHED_ALL => 'Xem toàn bộ thùng rác',
            self::VIEW_TRASHED_OWN => 'Xem thùng rác của mình',

            self::CREATE => 'Tạo công việc',

            self::UPDATE_ALL => 'Cập nhật mọi công việc',
            self::UPDATE_OWN => 'Cập nhật công việc của mình',

            self::COMPLETE_ALL => 'Hoàn thành mọi công việc',
            self::COMPLETE_OWN => 'Hoàn thành công việc của mình',

            self::DELETE_ALL => 'Xóa mọi công việc',
            self::DELETE_OWN => 'Xóa công việc của mình',

            self::RESTORE_ALL => 'Khôi phục mọi công việc',
            self::RESTORE_OWN => 'Khôi phục công việc của mình',

            self::FORCE_DELETE_ALL => 'Xóa vĩnh viễn công việc',
        };
    }
}
