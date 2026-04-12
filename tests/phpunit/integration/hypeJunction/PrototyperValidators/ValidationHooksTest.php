<?php

namespace hypeJunction\PrototyperValidators;

use Elgg\IntegrationTestCase;
use hypeJunction\Prototyper\Elements\Field;
use hypeJunction\Prototyper\Elements\ImageUploadField;
use hypeJunction\Prototyper\Elements\ValidationStatus;

/**
 * Pre-migration behavior lock for hypePrototyperValidators hook handlers.
 *
 * These tests lock in Elgg 3.x/4.x behavior of the procedural validation
 * functions declared in lib/hooks.php.  They do NOT fire the hooks through
 * elgg_trigger_plugin_hook() because the handlers are deliberately scoped
 * by the inline `if ($rule != "...")` guard to be safe when called with
 * arbitrary params.  We invoke the functions directly and assert the
 * returned ValidationStatus.
 */
class ValidationHooksTest extends IntegrationTestCase {

    public function getPluginID(): string {
        return '';
    }

    public function up() {
        // Vendored respect/validation ~0.9.0 ships Rules/String.php which is
        // unparseable on PHP 7+. The plugin cannot boot on PHP 7.4+. Skip the
        // entire suite until the dependency is upgraded. See bead elgg-migrate-9uea.
        if (PHP_VERSION_ID >= 70000) {
            $this->markTestSkipped('hypePrototyperValidators blocked by respect/validation ~0.9 on PHP 7+: Rules/String.php uses reserved word.');
        }
    }

    public function down() {}

    /* -------------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------------- */

    private function mockField(string $label = 'Test Field', string $type = 'text', bool $multiple = false, string $shortname = 'testfield'): Field {
        $field = $this->getMockBuilder(Field::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLabel', 'getType', 'isMultiple', 'getShortname'])
            ->getMockForAbstractClass();
        $field->method('getLabel')->willReturn($label);
        $field->method('getType')->willReturn($type);
        $field->method('isMultiple')->willReturn($multiple);
        $field->method('getShortname')->willReturn($shortname);
        return $field;
    }

    private function mockImageField(string $label = 'Image'): ImageUploadField {
        $field = $this->getMockBuilder(ImageUploadField::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLabel'])
            ->getMockForAbstractClass();
        $field->method('getLabel')->willReturn($label);
        return $field;
    }

    private function makeParams(Field $field, string $rule, $value, $expectation = null): array {
        return [
            'field' => $field,
            'rule' => $rule,
            'value' => $value,
            'expectation' => $expectation,
        ];
    }

    /* -------------------------------------------------------------------
     * prototyper_validate_type
     * ------------------------------------------------------------------- */

    public function testValidateTypeReturnsEarlyWhenFieldMissing(): void {
        $vs = new ValidationStatus();
        $result = prototyper_validate_type('validate:type', 'prototyper', $vs, []);
        $this->assertSame($vs, $result);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeReturnsEarlyForOtherRules(): void {
        $vs = new ValidationStatus();
        $params = $this->makeParams($this->mockField(), 'min', 'abc', 5);
        $result = prototyper_validate_type('validate:type', 'prototyper', $vs, $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeCreatesStatusWhenNull(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'hello', 'string');
        $result = prototyper_validate_type('validate:type', 'prototyper', null, $params);
        $this->assertInstanceOf(ValidationStatus::class, $result);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeStringAcceptsString(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'hello world', 'string');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeAlnumRejectsPunct(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'abc!!!', 'alnum');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeAlnumAcceptsAlphaNumeric(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'abc123', 'alnum');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeAlphaRejectsDigits(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'abc123', 'alpha');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeNumericAcceptsNumber(): void {
        $params = $this->makeParams($this->mockField(), 'type', '42.5', 'numeric');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeNumericRejectsString(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'abc', 'number');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeIntRejectsFloat(): void {
        $params = $this->makeParams($this->mockField(), 'type', '3.14', 'int');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeIntAcceptsInt(): void {
        $params = $this->makeParams($this->mockField(), 'type', '42', 'integer');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeDateAcceptsIsoDate(): void {
        $params = $this->makeParams($this->mockField(), 'type', '2020-01-15', 'date');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeDateRejectsGarbage(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'not-a-date', 'date');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeUrlAcceptsValid(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'https://example.com/path', 'url');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeUrlRejectsInvalid(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'not a url', 'url');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeEmailAcceptsValid(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'foo@example.com', 'email');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeEmailRejectsInvalid(): void {
        $params = $this->makeParams($this->mockField(), 'type', 'not-an-email', 'email');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeGuidAcceptsExistingEntity(): void {
        $user = $this->createUser();
        $params = $this->makeParams($this->mockField(), 'type', $user->guid, 'guid');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeGuidRejectsNonexistentEntity(): void {
        $params = $this->makeParams($this->mockField(), 'type', 999999999, 'guid');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateTypeImageAcceptsImageMime(): void {
        $params = $this->makeParams($this->mockField(), 'type', ['type' => 'image/png'], 'image');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateTypeImageRejectsNonImageMime(): void {
        $params = $this->makeParams($this->mockField(), 'type', ['type' => 'application/pdf'], 'image');
        $result = prototyper_validate_type('validate:type', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    /* -------------------------------------------------------------------
     * prototyper_validate_min / max
     * ------------------------------------------------------------------- */

    public function testValidateMinAcceptsEqualOrGreater(): void {
        $params = $this->makeParams($this->mockField(), 'min', 10, 5);
        $result = prototyper_validate_min('validate:min', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateMinRejectsLess(): void {
        $params = $this->makeParams($this->mockField(), 'min', 3, 5);
        $result = prototyper_validate_min('validate:min', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateMinReturnsEarlyForOtherRule(): void {
        $params = $this->makeParams($this->mockField(), 'max', 3, 5);
        $result = prototyper_validate_min('validate:min', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateMaxAcceptsEqualOrLess(): void {
        $params = $this->makeParams($this->mockField(), 'max', 4, 5);
        $result = prototyper_validate_max('validate:max', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateMaxRejectsGreater(): void {
        $params = $this->makeParams($this->mockField(), 'max', 99, 5);
        $result = prototyper_validate_max('validate:max', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    /* -------------------------------------------------------------------
     * prototyper_validate_minlength / maxlength
     * ------------------------------------------------------------------- */

    public function testValidateMinlengthAcceptsLongEnough(): void {
        $params = $this->makeParams($this->mockField(), 'minlength', 'hello world', 5);
        $result = prototyper_validate_minlength('validate:minlength', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateMinlengthRejectsTooShort(): void {
        $params = $this->makeParams($this->mockField(), 'minlength', 'hi', 5);
        $result = prototyper_validate_minlength('validate:minlength', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    public function testValidateMaxlengthAcceptsShortEnough(): void {
        $params = $this->makeParams($this->mockField(), 'maxlength', 'hi', 5);
        $result = prototyper_validate_maxlength('validate:maxlength', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateMaxlengthRejectsTooLong(): void {
        $params = $this->makeParams($this->mockField(), 'maxlength', 'this is too long', 5);
        $result = prototyper_validate_maxlength('validate:maxlength', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    /* -------------------------------------------------------------------
     * prototyper_validate_contains
     * ------------------------------------------------------------------- */

    public function testValidateContainsAcceptsSubstring(): void {
        $params = $this->makeParams($this->mockField(), 'contains', 'hello world', 'world');
        $result = prototyper_validate_contains('validate:contains', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateContainsRejectsMissingSubstring(): void {
        $params = $this->makeParams($this->mockField(), 'contains', 'hello world', 'xyz');
        $result = prototyper_validate_contains('validate:contains', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    /* -------------------------------------------------------------------
     * prototyper_validate_regex
     * ------------------------------------------------------------------- */

    public function testValidateRegexAcceptsMatching(): void {
        $params = $this->makeParams($this->mockField(), 'regex', 'abc123', '/^[a-z]+[0-9]+$/');
        $result = prototyper_validate_regex('validate:regex', 'prototyper', new ValidationStatus(), $params);
        $this->assertTrue($result->getStatus());
    }

    public function testValidateRegexRejectsNonMatching(): void {
        $params = $this->makeParams($this->mockField(), 'regex', 'ABC', '/^[a-z]+$/');
        $result = prototyper_validate_regex('validate:regex', 'prototyper', new ValidationStatus(), $params);
        $this->assertFalse($result->getStatus());
    }

    /* -------------------------------------------------------------------
     * prototyper_filter_input_view_vars
     * ------------------------------------------------------------------- */

    public function testFilterInputViewVarsReturnsUnchangedWhenNoField(): void {
        $return = ['foo' => 'bar'];
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', $return, []);
        $this->assertSame($return, $result);
    }

    public function testFilterInputViewVarsReturnsUnchangedWhenNoValidationRules(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn([]);
        $return = ['foo' => 'bar'];
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', $return, ['field' => $field]);
        $this->assertSame($return, $result);
    }

    public function testFilterInputViewVarsAddsParsleyTypeAttribute(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn(['type' => 'email']);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertArrayHasKey('data-parsley-type', $result);
        $this->assertSame('email', $result['data-parsley-type']);
        $this->assertArrayHasKey('data-parsley-trigger', $result);
    }

    public function testFilterInputViewVarsMapsAlnumTypeToAlphanum(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn(['type' => 'alnum']);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertSame('alphanum', $result['data-parsley-type']);
    }

    public function testFilterInputViewVarsMapsIntTypeToInteger(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn(['type' => 'int']);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertSame('integer', $result['data-parsley-type']);
    }

    public function testFilterInputViewVarsMapsRegexToPattern(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn(['regex' => '/^abc$/']);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertArrayHasKey('data-parsley-pattern', $result);
        $this->assertSame('/^abc$/', $result['data-parsley-pattern']);
    }

    public function testFilterInputViewVarsGenericRuleBecomesDataAttribute(): void {
        $field = $this->mockField();
        $field->method('getValidationRules')->willReturn(['minlength' => 5]);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertArrayHasKey('data-parsley-minlength', $result);
        $this->assertSame(5, $result['data-parsley-minlength']);
    }

    public function testFilterInputViewVarsAddsMultipleAttributeForCheckboxes(): void {
        $field = $this->mockField('label', 'checkboxes', false, 'mycheckboxes');
        $field->method('getValidationRules')->willReturn(['minlength' => 1]);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertArrayHasKey('data-parsley-multiple', $result);
        $this->assertSame('mycheckboxes', $result['data-parsley-multiple']);
    }

    public function testFilterInputViewVarsAddsMultipleAttributeForRadio(): void {
        $field = $this->mockField('label', 'radio', false, 'myradio');
        $field->method('getValidationRules')->willReturn(['minlength' => 1]);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertSame('myradio', $result['data-parsley-multiple']);
    }

    public function testFilterInputViewVarsAddsMultipleAttributeForIsMultipleField(): void {
        $field = $this->mockField('label', 'text', true, 'multifield');
        $field->method('getValidationRules')->willReturn(['minlength' => 1]);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertSame('multifield', $result['data-parsley-multiple']);
    }

    public function testFilterInputViewVarsHasNoMultipleForPlainText(): void {
        $field = $this->mockField('label', 'text', false, 'plain');
        $field->method('getValidationRules')->willReturn(['minlength' => 1]);
        $result = prototyper_filter_input_view_vars('input_vars', 'prototyper', [], ['field' => $field]);
        $this->assertArrayNotHasKey('data-parsley-multiple', $result);
    }

    /* -------------------------------------------------------------------
     * View existence / rendering
     * ------------------------------------------------------------------- */

    public function testValidationHelpViewExists(): void {
        $this->assertTrue(elgg_view_exists('prototyper/elements/validation'));
    }

    public function testJsValidationViewExists(): void {
        $this->assertTrue(elgg_view_exists('prototyper/elements/js_validation'));
    }

    public function testValidationHelpViewReturnsNothingWhenNoField(): void {
        $out = elgg_view('prototyper/elements/validation', []);
        $this->assertSame('', trim($out));
    }

    public function testJsValidationViewRendersWithoutError(): void {
        $out = elgg_view('prototyper/elements/js_validation', []);
        $this->assertIsString($out);
    }

    /* -------------------------------------------------------------------
     * Hook registrations (elgg-plugin.php)
     * ------------------------------------------------------------------- */

    public function testValidateTypeHookRegistered(): void {
        $this->assertTrue(
            _elgg_services()->hooks->hasHandler('validate:type', 'prototyper')
        );
    }

    public function testValidateMinMaxHooksRegistered(): void {
        $hooks = _elgg_services()->hooks;
        $this->assertTrue($hooks->hasHandler('validate:min', 'prototyper'));
        $this->assertTrue($hooks->hasHandler('validate:max', 'prototyper'));
    }

    public function testValidateLengthHooksRegistered(): void {
        $hooks = _elgg_services()->hooks;
        $this->assertTrue($hooks->hasHandler('validate:minlength', 'prototyper'));
        $this->assertTrue($hooks->hasHandler('validate:maxlength', 'prototyper'));
    }

    public function testValidateContainsRegexHooksRegistered(): void {
        $hooks = _elgg_services()->hooks;
        $this->assertTrue($hooks->hasHandler('validate:contains', 'prototyper'));
        $this->assertTrue($hooks->hasHandler('validate:regex', 'prototyper'));
    }

    public function testInputVarsHookRegistered(): void {
        $this->assertTrue(
            _elgg_services()->hooks->hasHandler('input_vars', 'prototyper')
        );
    }
}
