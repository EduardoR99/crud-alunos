<?php

namespace Tests\Unit\Rules;

use App\Rules\StudentRules;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class StudentRulesTest extends CIUnitTestCase
{
    public function testGetReturnsRequiredFields(): void
    {
        $rules = StudentRules::get();

        $this->assertArrayHasKey('student.nome_completo', $rules);
        $this->assertArrayHasKey('student.cpf', $rules);
        $this->assertStringContainsString('required', $rules['student.nome_completo']);
        $this->assertStringContainsString('required', $rules['student.cpf']);
    }

    public function testCpfUniqueRuleWithoutIgnoreId(): void
    {
        $rules = StudentRules::get();

        $this->assertStringContainsString('is_unique[students.cpf]', $rules['student.cpf']);
        $this->assertStringNotContainsString(',id,', $rules['student.cpf']);
    }

    public function testCpfUniqueRuleWithIgnoreId(): void
    {
        $rules = StudentRules::get(42);

        $this->assertStringContainsString('is_unique[students.cpf,id,42]', $rules['student.cpf']);
    }

    public function testContactEmailValidation(): void
    {
        $rules = StudentRules::get();

        $this->assertArrayHasKey('contacts.*.email', $rules);
        $this->assertStringContainsString('valid_email', $rules['contacts.*.email']);
    }

    public function testAddressFieldsPresent(): void
    {
        $rules = StudentRules::get();

        $this->assertArrayHasKey('addresses.*.cep', $rules);
        $this->assertArrayHasKey('addresses.*.estado', $rules);
    }
}
