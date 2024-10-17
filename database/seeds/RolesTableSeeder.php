<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => 1, 'role_name' => 'Admin', 'role_name_ar' => 'ادمن'],
            ['id' => 2, 'role_name' => 'Client', 'role_name_ar' => 'عميل']
        ];

        foreach ($records as $key => $record) {
            $exists = DB::table('roles')->where('id', $record['id'])->first();
            if (!$exists) {
                DB::table('roles')->insert([$record]);
            } else {
                DB::table('roles')
                    ->where('id', $record['id'])
                    ->update([
                        'role_name' => $record['role_name'],
                        'role_name_ar' => $record['role_name_ar'],
                    ]);
            }
        }
    }
}
