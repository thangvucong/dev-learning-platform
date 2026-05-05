<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDeprecatedCourseOrderClassColumns extends Migration
{
    /**
     * Gỡ các cột không còn dùng theo schema mới (courses / orders / classes).
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('courses', 'level_id')) {
            $this->dropForeignKeyIfExists('courses', 'level_id');
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('level_id');
            });
        }

        Schema::table('courses', function (Blueprint $table) {
            foreach (['duration', 'is_free', 'deleted_at'] as $column) {
                if (Schema::hasColumn('courses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasColumn('orders', 'currency_id')) {
            $this->dropForeignKeyIfExists('orders', 'currency_id');
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('currency_id');
            });
        }

        if (Schema::hasColumn('classes', 'instructor_id')) {
            $this->dropForeignKeyIfExists('classes', 'instructor_id');
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('instructor_id');
            });
        }

        Schema::table('classes', function (Blueprint $table) {
            foreach (['mode', 'capacity'] as $column) {
                if (Schema::hasColumn('classes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations (nullable / default để tránh lỗi khi có dữ liệu).
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('course_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('classes', 'mode')) {
                $table->string('mode')->default('zoom')->after('code');
            }
            if (!Schema::hasColumn('classes', 'capacity')) {
                $table->unsignedInteger('capacity')->default(30)->after('status');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->after('total_amount')->constrained()->nullOnDelete();
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'level_id')) {
                $table->foreignId('level_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('courses', 'duration')) {
                $table->unsignedInteger('duration')->nullable()->after('intro_video_url');
            }
            if (!Schema::hasColumn('courses', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('status');
            }
            if (!Schema::hasColumn('courses', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Xóa FK đúng tên constraint (MySQL giữ tên cũ sau RENAME TABLE, ví dụ course_classes_*).
     *
     * @param  string  $table Tên bảng không gồm prefix (như Schema::table).
     * @param  string  $column
     * @return void
     */
    protected function dropForeignKeyIfExists(string $table, string $column): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            $database = $connection->getDatabaseName();
            $tableName = $connection->getTablePrefix() . $table;

            $rows = $connection->select(
                'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
                 AND REFERENCED_TABLE_NAME IS NOT NULL',
                [$database, $tableName, $column]
            );

            $seen = [];
            foreach ($rows as $row) {
                $arr = (array) $row;
                $name = $arr['CONSTRAINT_NAME'] ?? $arr['constraint_name'] ?? null;
                if (!$name || isset($seen[$name])) {
                    continue;
                }
                $seen[$name] = true;
                $connection->statement(
                    sprintf('ALTER TABLE `%s` DROP FOREIGN KEY `%s`', $tableName, $name)
                );
            }

            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropForeign([$column]);
        });
    }
}
