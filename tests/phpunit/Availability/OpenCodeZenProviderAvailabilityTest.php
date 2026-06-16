<?php
/**
 * Tests for OpenCodeZenProviderAvailability.
 *
 * @package AlAminAhamed\OpenCodeZenAiProvider\Tests\Availability
 */

declare(strict_types=1);

namespace AlAminAhamed\OpenCodeZenAiProvider\Tests\Availability;

use AlAminAhamed\OpenCodeZenAiProvider\Availability\OpenCodeZenProviderAvailability;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;

/**
 * Class OpenCodeZenProviderAvailabilityTest
 *
 * @since 1.3.2
 */
class OpenCodeZenProviderAvailabilityTest extends TestCase {

	/**
	 * @var OpenCodeZenProviderAvailability
	 */
	private $availability;

	/**
	 * Set up test fixtures.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		$this->availability = new OpenCodeZenProviderAvailability();
	}

	/**
	 * Tear down Brain Monkey and clear env vars after each test.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test class implements ProviderAvailabilityInterface.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_implements_interface(): void {
		$this->assertInstanceOf( ProviderAvailabilityInterface::class, $this->availability );
	}

	/**
	 * Test isConfigured returns false when no API key source is present.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_by_default(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->justReturn( '' );

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns true when ApiKeyRequestAuthentication has a non-empty key.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_true_with_api_key_auth(): void {
		$this->availability->setRequestAuthentication(
			new ApiKeyRequestAuthentication( 'sk-test' )
		);

		$this->assertTrue( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns false when ApiKeyRequestAuthentication has an empty key.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_empty_api_key_auth(): void {
		$this->availability->setRequestAuthentication(
			new ApiKeyRequestAuthentication( '' )
		);

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns true when ApiKeyRequestAuthentication has a whitespace-only key.
	 *
	 * PHP empty() treats non-empty strings (even whitespace) as truthy, so
	 * '   ' passes the !empty() check and isConfigured() returns true.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_whitespace_api_key_auth(): void {
		$this->availability->setRequestAuthentication(
			new ApiKeyRequestAuthentication( '   ' )
		);

		$this->assertTrue( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns true when OPENCODE_ZEN_API_KEY env var is set.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_true_with_env_key(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=secret-env-key' );

		$this->assertTrue( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns false when OPENCODE_ZEN_API_KEY env var is empty.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_empty_env_key(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->justReturn( '' );

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns true when connectors option has a non-empty key.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_true_with_connectors_option(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->alias(
			static function ( string $option, $default = false ) {
				if ( 'connectors_ai_opencode_zen_api_key' === $option ) {
					return 'key123';
				}
				return $default;
			}
		);

		$this->assertTrue( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured checks legacy credentials when connectors option is empty.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_empty_connectors_option(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->alias(
			static function ( string $option, $default = false ) {
				if ( 'connectors_ai_opencode_zen_api_key' === $option ) {
					return '';
				}
				if ( 'wp_ai_client_credentials' === $option ) {
					return array();
				}
				return $default;
			}
		);

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns true when legacy wp_ai_client_credentials has a key.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_true_with_legacy_credentials(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->alias(
			static function ( string $option, $default = false ) {
				if ( 'connectors_ai_opencode_zen_api_key' === $option ) {
					return '';
				}
				if ( 'wp_ai_client_credentials' === $option ) {
					return array( 'opencode-zen' => array( 'api_key' => 'mykey' ) );
				}
				return $default;
			}
		);

		$this->assertTrue( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns false when legacy credentials have an empty api_key.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_empty_legacy_key(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->alias(
			static function ( string $option, $default = false ) {
				if ( 'connectors_ai_opencode_zen_api_key' === $option ) {
					return '';
				}
				if ( 'wp_ai_client_credentials' === $option ) {
					return array( 'opencode-zen' => array( 'api_key' => '' ) );
				}
				return $default;
			}
		);

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns false when wp_ai_client_credentials is not an array.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_with_non_array_legacy_credentials(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->alias(
			static function ( string $option, $default = false ) {
				if ( 'connectors_ai_opencode_zen_api_key' === $option ) {
					return '';
				}
				if ( 'wp_ai_client_credentials' === $option ) {
					return 'not-array';
				}
				return $default;
			}
		);

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test isConfigured returns false when all option sources return empty values.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_is_configured_false_when_all_options_empty(): void {
		putenv( 'OPENCODE_ZEN_API_KEY=' );

		Functions\when( 'get_option' )->justReturn( '' );

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test auth object with empty key returns false even when env var is set.
	 *
	 * Auth object has highest priority; an empty key short-circuits the env var check.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_api_key_auth_takes_priority_over_env(): void {
		$this->availability->setRequestAuthentication(
			new ApiKeyRequestAuthentication( '' )
		);

		putenv( 'OPENCODE_ZEN_API_KEY=should-not-be-reached' );

		$this->assertFalse( $this->availability->isConfigured() );
	}

	/**
	 * Test setRequestAuthentication does not throw for a valid auth object.
	 *
	 * @since 1.3.2
	 *
	 * @return void
	 */
	public function test_set_request_authentication_accepts_auth_object(): void {
		$this->expectNotToPerformAssertions();

		$this->availability->setRequestAuthentication(
			new ApiKeyRequestAuthentication( 'sk-any-value' )
		);
	}
}
