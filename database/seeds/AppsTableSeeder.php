<?php

use Illuminate\Database\Seeder;

class AppsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => 1,'app_name' => 'Mjlsi']
        ];

        foreach ($records as $key => $record) {
            $exists = DB::table('apps')->where('id', $record['id'])->first();
            if (!$exists) {
                DB::table('apps')->insert([$record]);
            } else {
                DB::table('apps')
                    ->where('id', $record['id'])
                    ->update([
                        'app_name' => $record['app_name'],
                    ]);
            }
        }
    }
}
