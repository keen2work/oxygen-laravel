<?php

namespace Tests\Browser\Auth;

use App\Providers\RouteServiceProvider;
use EMedia\TestKit\Traits\InteractsWithUsers;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Features;
use Tests\Browser\Pages\ForgotPassword;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Tests\Browser\Pages\ResetPassword;
use Laravel\Dusk\Browser;
use EMedia\TestKit\Mail\Traits\LogMailTracking;

class ForgotPasswordTest extends DuskTestCase
{

	use InteractsWithUsers;
	use LogMailTracking;

	/**
	 * It should not allow sending password reset email for invalid email.
	 *
	 * @return void
	 */
	public function testNotSendingForInvalidEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'johndoe.com')
				->click('@submit')
				->assertPathIs('/password/forgot');
		});
	}

	/**
	 * It should not allow sending password reset email for non-existing email.
	 *
	 * @return void
	 */
	public function testNotSendingForNonExistingEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'xyz@nonexisting.com')
				->click('@submit')
				->assertPathIs('/password/forgot')
				->assertSee(trans('passwords.user'));
		});
	}

	/**
	 * It should send password reset email to existing email.
	 *
	 * @return void
	 */
	public function testSendingForExistingEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'apps+user@elegantmedia.com.au')
				->click('@submit')
				->waitForRoute('password.request', [], 15)
				->assertSee(trans('passwords.sent'));
		});
	}

	/**
	 *
	 * Test that the forgot password page sends a valid email with a reset link
	 *
	 * @return void
	 * @throws \Throwable
	 */
	public function testForgotPasswordSendsValidEmailWithResetLink(): void
	{
		$this->withoutMiddleware('throttle');

		$user = $this->findUserByEmail('apps@elegantmedia.com.au');

		// verify the current mail driver is set to `log`
		$this->assertEquals('log', config('mail.default'));

		// clear any existing password reset tokens for this user
		$passwordBroker = $this->app->make('auth.password.broker');
		$passwordBroker->deleteToken($user);

		// using Dusk, go to password reset page and send a password reset email
		$this->browse(function (Browser $browser) use ($user, $passwordBroker) {
			$browser->visitRoute('password.request')
				->assertSee(__('Reset Password'))
				->type('#email', $user->email)
				->press(__('Send Password Reset Link'))
				// ->waitForRoute('password.request', [], 5)
				->assertRouteIs('password.request')
				->assertSee(__(Password::RESET_LINK_SENT))
			;

			// get the latest password reset token for this user
			$token = $passwordBroker->getRepository()->recentlyCreatedToken($user);

			// it must not be null
			$this->assertTrue($token);

			$subjectToFind = __('Reset Password Notification');
			$stringToFind1 = 'You are receiving this email because we received a password reset request for your account.';
			$stringToFind2 = route('password.reset', '');

			$this->seeLastEmailSubject($subjectToFind);
			$this->seeLastEmailContains($stringToFind1);
			$this->seeLastEmailContains($stringToFind2);
		});
	}

	public function testForgotPasswordGeneratesValidResetLink(): void
	{
		$this->withoutMiddleware('throttle');

		$user = $this->findUserByEmail('apps@elegantmedia.com.au');

		$passwordBroker = $this->app->make('auth.password.broker');
		$token = $passwordBroker->createToken($user);

		$this->assertTrue($passwordBroker->tokenExists($user, $token));

		$this->browse(function (Browser $browser) use ($user, $token) {
			$newPassword = '123-123-123';

			$browser->visit(route('password.reset', $token))
				->assertPathBeginsWith('/password/reset')
				->assertSee('Reset Password')
				->type('#email', $user->email)
				->type('#password', $newPassword)
				->type('#password-confirm', $newPassword)
				->click('#reset-button')
				->assertDontSee('token is invalid')
				->waitForRoute('login', [], 5)
				->assertRouteIs('login')
				->assertSee(trans('passwords.reset'));

			// check the user can't login with old password after a change
			$browser->visit(new Login())
				->type('@email', $user->email)
				->type('@password', '12345678')
				->click('@submit')
				->assertPathIs('/login')
				->assertSee('These credentials do not match our records')
				->pause(1000);

			// check user can login with new password
			$browser->visit(new Login())
				->type('@email', $user->email)
				->type('@password', $newPassword)
				->click('@submit')
				->assertDontSeeLink('Login');

			// if email verification is enabled, the user should get redirected to `verification.notice` link
			if (Features::enabled(Features::emailVerification())) {
				$browser->assertRouteIs('verification.notice');
			} else {
				$browser->assertPathIs(RouteServiceProvider::HOME);
			}
			$browser->click('#navbarDropdown')
				->click('#logout');
			$browser->assertSee(config('app.name'));

			// restore password
			$user->password = bcrypt('12345678');
			$user->save();
		});
	}

	/**
	 *
	 * Reset the changes
	 *
	 */
	protected function tearDown() : void
	{
		\DB::table('password_reset_tokens')->where('email', 'apps+user@elegantmedia.com.au')->delete();

		parent::tearDown();
	}
}
