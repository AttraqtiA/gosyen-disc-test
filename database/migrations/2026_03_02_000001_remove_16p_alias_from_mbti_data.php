<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('test_sessions')) {
            DB::table('test_sessions')
                ->where('test_type', '16P')
                ->update(['test_type' => 'MBTI']);
        }

        if (Schema::hasTable('position_mbti_profiles')) {
            $rows = DB::table('position_mbti_profiles')
                ->where('test_type', '16P')
                ->get(['id', 'position_id']);

            foreach ($rows as $row) {
                $alreadyExists = DB::table('position_mbti_profiles')
                    ->where('position_id', $row->position_id)
                    ->where('test_type', 'MBTI')
                    ->exists();

                if ($alreadyExists) {
                    DB::table('position_mbti_profiles')->where('id', $row->id)->delete();
                    continue;
                }

                DB::table('position_mbti_profiles')
                    ->where('id', $row->id)
                    ->update(['test_type' => 'MBTI']);
            }
        }

        if (Schema::hasTable('mbti_recommendations')) {
            DB::table('mbti_recommendations')
                ->where('test_type', '16P')
                ->update(['test_type' => 'MBTI']);
        }
    }

    public function down(): void
    {
    }
};
