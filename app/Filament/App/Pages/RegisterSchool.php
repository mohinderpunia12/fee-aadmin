<?php

namespace App\Filament\App\Pages;

use App\Models\School;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterSchool extends Page
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.app.pages.register-school';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('school_name')
                    ->label('School Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('School URL (slug)')
                    ->required()
                    ->maxLength(255)
                    ->unique(School::class, 'slug')
                    ->alphaDash()
                    ->helperText('This will be your unique school identifier in the URL'),
                TextInput::make('domain')
                    ->label('School Domain (Optional)')
                    ->maxLength(255)
                    ->url()
                    ->placeholder('example.com'),
                FileUpload::make('logo')
                    ->label('School Logo (Optional)')
                    ->image()
                    ->directory('school-logos')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Upload your school logo. This will appear on fee receipts and ID cards.'),
                TextInput::make('admin_name')
                    ->label('Your Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('admin_email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email')
                    ->helperText('This will be your login email'),
                TextInput::make('admin_password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->confirmed(),
                TextInput::make('admin_password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function register(): void
    {
        $data = $this->form->getState();

        // Create school with 7-day trial
        $school = School::create([
            'name' => $data['school_name'],
            'slug' => $data['slug'] ?? Str::slug($data['school_name']),
            'domain' => $data['domain'] ?? null,
            'logo' => $data['logo'] ?? null,
        ]);

        // Activate 7-day trial
        $school->activateTrial(7);

        // Create admin user for the school
        $user = User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
            'school_id' => $school->id,
        ]);

        // Login the user
        auth()->login($user);

        // Redirect to school dashboard
        $this->redirect('/app/' . $school->slug);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function getTitle(): string
    {
        return 'Register Your School';
    }

    public function getHeading(): string
    {
        return 'Start Your 7-Day Free Trial';
    }

    public function getSubheading(): ?string
    {
        return 'Create your school account and get full access to all features for 7 days. No credit card required.';
    }
}