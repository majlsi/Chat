<?php

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => 1, 'module_name' => 'Control Panel', 'module_name_ar' => 'لوحه التحكم', 'icon'=>''],
            ['id' => 2, 'module_name' => 'Security', 'module_name_ar' => 'الحمايه', 'icon'=>''],
        ];

        foreach ($records as $key => $record) {
            $exists = DB::table('modules')->where('id', $record['id'])->first();
            if (!$exists) {
                DB::table('modules')->insert([$record]);
            } else {
                DB::table('modules')
                    ->where('id', $record['id'])
                    ->update([
                        'module_name' => $record['module_name'],
                        'module_name_ar' => $record['module_name_ar'],
                        'icon' => $record['icon'],
                    ]);
            }
        }
    }
}
