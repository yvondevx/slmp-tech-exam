<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Address;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Photo;
use App\Models\Post;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FetchJsonPlaceholder extends Command
{
    protected $signature = 'fetch:jsonplaceholder';
    protected $description = 'Fetch and store data from jsonplaceholder.typicode.com';

    public function handle(): int
    {
        $this->info('Starting data fetch from jsonplaceholder...');

        DB::transaction(function () {
            $this->importUsers();
            $this->importPosts();
            $this->importComments();
            $this->importAlbums();
            $this->importPhotos();
            $this->importTodos();
        });

        $this->info('Data fetch complete.');

        return 0;
    }

    protected function importUsers(): void
    {
        $users = Http::get('https://jsonplaceholder.typicode.com/users')->throw()->json();

        foreach ($users as $item) {
            $user = User::query()
                ->where('external_id', $item['id'])
                ->orWhere('email', $item['email'])
                ->first();

            if (!$user) {
                $user = new User();
            }

            $user->external_id = $item['id'];
            $user->fill([
                'name' => $item['name'],
                'username' => $item['username'] ?? null,
                'email' => $item['email'],
                'phone' => $item['phone'] ?? null,
                'website' => $item['website'] ?? null,
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', $item['username'] . '@' . Str::random(32)),
            ]);
            $user->save();

            Address::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'street' => $item['address']['street'] ?? null,
                    'suite' => $item['address']['suite'] ?? null,
                    'city' => $item['address']['city'] ?? null,
                    'zipcode' => $item['address']['zipcode'] ?? null,
                    'lat' => $item['address']['geo']['lat'] ?? null,
                    'lng' => $item['address']['geo']['lng'] ?? null,
                ]
            );

            Company::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $item['company']['name'] ?? null,
                    'catch_phrase' => $item['company']['catchPhrase'] ?? null,
                    'bs' => $item['company']['bs'] ?? null,
                ]
            );
        }

        $this->info('Users imported: ' . count($users));
    }

    protected function importPosts(): void
    {
        $posts = Http::get('https://jsonplaceholder.typicode.com/posts')->throw()->json();

        foreach ($posts as $item) {
            $user = User::where('external_id', $item['userId'])->first();
            if (!$user) {
                continue;
            }

            Post::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'user_id' => $user->id,
                    'title' => $item['title'],
                    'body' => $item['body'],
                ]
            );
        }

        $this->info('Posts imported: ' . count($posts));
    }

    protected function importComments(): void
    {
        $comments = Http::get('https://jsonplaceholder.typicode.com/comments')->throw()->json();

        foreach ($comments as $item) {
            $post = Post::where('external_id', $item['postId'])->first();
            if (!$post) {
                continue;
            }

            Comment::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'post_id' => $post->id,
                    'name' => $item['name'],
                    'email' => $item['email'],
                    'body' => $item['body'],
                ]
            );
        }

        $this->info('Comments imported: ' . count($comments));
    }

    protected function importAlbums(): void
    {
        $albums = Http::get('https://jsonplaceholder.typicode.com/albums')->throw()->json();

        foreach ($albums as $item) {
            $user = User::where('external_id', $item['userId'])->first();
            if (!$user) {
                continue;
            }

            Album::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'user_id' => $user->id,
                    'title' => $item['title'],
                ]
            );
        }

        $this->info('Albums imported: ' . count($albums));
    }

    protected function importPhotos(): void
    {
        $photos = Http::get('https://jsonplaceholder.typicode.com/photos')->throw()->json();

        foreach ($photos as $item) {
            $album = Album::where('external_id', $item['albumId'])->first();
            if (!$album) {
                continue;
            }

            Photo::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'album_id' => $album->id,
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'thumbnail_url' => $item['thumbnailUrl'],
                ]
            );
        }

        $this->info('Photos imported: ' . count($photos));
    }

    protected function importTodos(): void
    {
        $todos = Http::get('https://jsonplaceholder.typicode.com/todos')->throw()->json();

        foreach ($todos as $item) {
            $user = User::where('external_id', $item['userId'])->first();
            if (!$user) {
                continue;
            }

            Todo::updateOrCreate(
                ['external_id' => $item['id']],
                [
                    'user_id' => $user->id,
                    'title' => $item['title'],
                    'completed' => $item['completed'],
                ]
            );
        }

        $this->info('Todos imported: ' . count($todos));
    }
}
