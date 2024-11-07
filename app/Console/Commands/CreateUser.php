<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
        protected $signature = 'user:create {name} {email} {password} {--image=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $imagePath = $this->option('image');

        $request = Request::create('/register', 'POST', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        if ($imagePath && file_exists($imagePath)) {
            $request->files->set('image', new \Illuminate\Http\UploadedFile(
                $imagePath,
                pathinfo($imagePath, PATHINFO_BASENAME)
            ));
        }

        $controller = new RegisteredUserController();

            return $controller->store($request);
    }
}
