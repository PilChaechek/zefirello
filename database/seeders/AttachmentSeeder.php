<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();

        if ($tasks->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Убедиться, что директория существует
        Storage::disk('public')->makeDirectory('attachments');

        foreach ($tasks as $task) {
            // Загрузить 2-3 случайных изображения
            for ($i = 0; $i < rand(2, 3); $i++) {
                $imageUrl = 'https://picsum.photos/800/600?random=' . uniqid();
                $response = Http::get($imageUrl);

                if ($response->successful()) {
                    $contents = $response->body();
                    $originalName = 'image_' . uniqid() . '.jpg';
                    $path = 'attachments/' . $originalName;

                    Storage::disk('public')->put($path, $contents);

                    $task->attachments()->create([
                        'user_id' => $users->random()->id,
                        'path' => $path,
                        'original_name' => $originalName,
                        'mime_type' => 'image/jpeg',
                        'size' => strlen($contents),
                    ]);
                }
            }
        }
    }
}
