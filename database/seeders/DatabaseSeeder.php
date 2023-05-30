<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->generateRooms();
    }

    public function generateRooms()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('rooms')->truncate();
        $rooms = [
            "Flutter",
            "Laravel",
            "Nest Js",
            "Node Js",
            "React Js",
            "Angular Js",
            "Vue Js",
            "Golang",
            "Python",
            "Php",
            "Java",
            "Ruby",
            "Docker",
            "Dev Oops",
            "K8s",
            "Aws Service",
            "Azure",
            "Macbook",
            "Dell Xps",
            "Vb",
            ".Net",
            "Unity",
            "C#",
            "C++",
            "C",
            "Kotin",
            "Swift",
            "Shell",
            "Css",
            "Mysql"
        ];

        foreach ($rooms as $key => $room) {
            Room::create([
                "name" => $room
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
