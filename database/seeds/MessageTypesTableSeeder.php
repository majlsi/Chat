<?php

use Illuminate\Database\Seeder;

class MessageTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id' => 1,'message_type_name' =>'Text'],
            ['id' => 2,'message_type_name' =>'Attachment'],
        ];

        foreach ($records as $key => $record) {
            $exists = DB::table('message_types')->where('id', $record['id'])->first();
            if (!$exists) {
                DB::table('message_types')->insert([$record]);
            } else {
                DB::table('message_types')
                    ->where('id', $record['id'])
                    ->update([
                        'message_type_name' => $record['message_type_name']
                    ]);
            }
        }
    }
}
