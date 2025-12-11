<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        Log::info('Google OAuth: handleGoogleCallback controller reached');
        try {
            Log::info('Google OAuth: Callback started');
            $googleUser = Socialite::driver('google')->user();
            Log::info('Google OAuth: User fetched', ['googleUser' => $googleUser]);

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $nickname = $googleUser->getNickname();
            $givenName = $googleUser->user['given_name'] ?? null;
            $familyName = $googleUser->user['family_name'] ?? null;


            // Generate a unique username fallback
            $baseUsername = $nickname ?: ($givenName ? Str::slug($givenName) : null) ?: (explode('@', $email)[0] ?? null) ?: 'google_' . $googleId;
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->where('email', '!=', $email)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            $firstName = $givenName ?: (explode('@', $email)[0] ?? 'GoogleUser');
            $lastName = $familyName ?: 'User';

            Log::info('Google OAuth: User data prepared', [
                'googleId' => $googleId,
                'email' => $email,
                'username' => $username,
                'firstName' => $firstName,
                'lastName' => $lastName,
            ]);

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'username' => $username,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => bcrypt(uniqid('google_', true)),
                    'email_verified_at' => now(),
                ]
            );
            Log::info('Google OAuth: User created/updated', ['user' => $user]);

            // Download Google avatar 
            if (!$user->profile_picture && $googleUser->getAvatar()) {
                $googleAvatarUrl = $googleUser->getAvatar();
                $response = Http::get($googleAvatarUrl);
                if ($response->ok()) {
                    $filename = 'avatars/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($filename, $response->body());
                    $user->profile_picture = $filename;
                    $user->save();
                    Log::info('Google OAuth: Avatar saved', ['filename' => $filename]);
                } else {
                    Log::warning('Google OAuth: Failed to download avatar', ['url' => $googleAvatarUrl]);
                }
            }

            // Login the user
            Auth::login($user, true);
            Log::info('Google OAuth: User logged in', ['user_id' => $user->id]);

            return redirect()->route('products.index')->with('success', 'Logged in with Google successfully.');

        } catch (\Exception $e) {
            Log::error('Google OAuth error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }
}
