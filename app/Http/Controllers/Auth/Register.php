<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\User;
use App\Models\Common\Company;
use Illuminate\Support\Str;
use App\Abstracts\Http\Controller;
use App\Jobs\Auth\DeleteInvitation;
use App\Models\Auth\UserInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\Register as Request;
use App\Utilities\Installer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Register extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');  // Ensure only guests can access registration
    }

    /**
     * Display the registration form.
     *
     * @return \Illuminate\View\View
     */
    // public function create($token)
    public function create()
    {
        return view('auth.register.create');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Http\Requests\Auth\Register  $request
     * @return \Illuminate\Http\RedirectResponse
     */

     public function store(Request $request)
     {
        
        //dd("Hello");


         try {
             $validated = $request->validate([
                 'company_name'  => 'required|string|max:255',
                 'company_email' => 'required|email|max:255|unique:companies,email',
                 'user_password' => 'required|string|min:8',
             ]);
         } catch (\Illuminate\Validation\ValidationException $e) {
             return redirect()->back()->withErrors($e->errors());
         }
     
         try {
             DB::transaction(function () use ($request, $validated) {

                 // Create the company
                 $company = Company::create([
                     'name'  => $request->input('company_name'),
                 ]);
     
                 // Create an admin user for the company
                 $user = User::create([
                     'name'       => 'Admin',
                     'email'      => $request->input('email'),
                     'password'   => Hash::make($request->input('user_password')),
                     'company_id' => $company->id,
                 ]);

                 
                 // Seed default data for the company
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Accounts', '--company' => $company->id]);
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Categories', '--company' => $company->id]);
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Currencies', '--company' => $company->id]);
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\EmailTemplates', '--company' => $company->id]);
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Modules', '--company' => $company->id]);
                 Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Reports', '--company' => $company->id]);
                 
                //  $email = $request->input('company_email'); // Get email from the form
                 Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeds\\Settings',
                    '--company' => $company->id,
                ]);
            
                 // Fire the registered event
                 event(new Registered($user));

                // return redirect()->route('login')->with('success', 'Company and admin user created successfully!');

             });
     
             return redirect()->route('login')->with('success', 'Company and admin user created successfully!');
         } catch (\Exception $e) {
             Log::error('Error while creating company: ' . $e->getMessage());
     
             return redirect()->back()->withErrors([
                 'error' => 'An error occurred during the registration process. Please try again.',
             ]);
         }
     }
}
