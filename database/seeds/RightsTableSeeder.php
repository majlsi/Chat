<?php

use Illuminate\Database\Seeder;

class RightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => 1,'right_name' =>'Roles', 'right_name_ar'=>'الادوار', 'module_id'=>2, 'right_url'=>'#/Roles', 'right_order_number'=>1, 'in_menu'=>1, 'icon'=>''],
            ['id' => 2,'right_name' =>'RoleAdd', 'right_name_ar'=>'اضافه دور', 'module_id'=>2, 'right_url'=>'#/Role', 'right_order_number'=>2, 'in_menu'=>0, 'icon'=>''],
            ['id' => 3,'right_name' =>'RoleEdit', 'right_name_ar'=>'تعديل دور', 'module_id'=>2, 'right_url'=>'#/Role/:id', 'right_order_number'=>3, 'in_menu'=>0, 'icon'=>''],
        ];

        foreach ($records as $key => $record) {
            $exists = DB::table('rights')->where('id', $record['id'])->first();
            if (!$exists) {
                DB::table('rights')->insert([$record]);
            } else {
                DB::table('rights')
                    ->where('id', $record['id'])
                    ->update([
                        'right_name' => $record['right_name'],
                        'right_name_ar' => $record['right_name_ar'],
                        'module_id' => $record['module_id'],
                        'right_url' => $record['right_url'],
                        'right_order_number' => $record['right_order_number'],
                        'in_menu' => $record['in_menu'],
                        'icon' => $record['icon']
                    ]);
            }
        }
    }
}
