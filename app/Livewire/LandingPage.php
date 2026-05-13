<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LandingPage extends Component
{
    public $showSignup = false;
    public $name = '';
    public $email = '';
    public $password = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ];

    public function mount($locale = null)
    {
        if ($locale && in_array($locale, ['ar'])) {
            Session::put('locale', $locale);
            app()->setLocale($locale);
        } elseif (!$locale) {
            Session::put('locale', 'en');
            app()->setLocale('en');
        }
    }

    public function openSignup()
    {
        $this->showSignup = true;
    }

    public function closeSignup()
    {
        $this->showSignup = false;
        $this->reset(['name', 'email', 'password']);
        $this->resetErrorBag();
    }

    public function switchLanguage($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);
        }
        // Redirect to the appropriate route
        return redirect()->route('landing', $locale === 'ar' ? ['locale' => 'ar'] : []);
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.landing-page')
            ->layout('components.layouts.landing');
    }
}
