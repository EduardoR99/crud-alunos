<?php

namespace App\Controllers\Api;

use App\DTOs\Request\CreateStudentRequest;
use App\DTOs\Request\UpdateStudentRequest;
use App\DTOs\Request\StudentAddressRequest;
use App\DTOs\Request\StudentContactRequest;
use App\Enums\Sexo;
use App\Enums\TipoEndereco;
use App\Exceptions\DeletedStudentConflictException;
use App\Rules\StudentRules;
use App\Traits\ApiResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;

class StudentController extends ResourceController
{
    use ApiResponseTrait;

    public function index(): ResponseInterface
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 15);
        $search  = $this->request->getGet('search');

        $result = Services::studentService()->list($perPage, $search);

        $studentsData = array_map(fn($student) => $student->toArray(), $result['students']);

        return $this->success(
            data: $studentsData,
            meta: [
                'current_page' => $result['currentPage'],
                'per_page'     => $result['perPage'],
                'total'        => $result['total'],
                'page_count'   => $result['pageCount'],
            ],
        );
    }

    public function show($id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->error('ID do aluno é obrigatório.');
        }

        $student = Services::studentService()->findById($id);

        if ($student === null) {
            return $this->notFound('Aluno não encontrado.');
        }

        return $this->success($student->toArray());
    }

    public function create(): ResponseInterface
    {
        if (!$this->validate(StudentRules::get())) {
            return $this->validationError($this->validator->getErrors());
        }

        $studentData = $this->request->getJsonVar('student', true);
        $contacts    = $this->request->getJsonVar('contacts', true) ?? [];
        $addresses   = $this->request->getJsonVar('addresses', true) ?? [];

        $contactDTOs = array_map(fn($c) => new StudentContactRequest(...$c), $contacts);
        $addressDTOs = array_map(fn($a) => new StudentAddressRequest(
            ...[...$a, 'tipo_endereco' => TipoEndereco::from($a['tipo_endereco'] ?? 'residencial')]
        ), $addresses);

        $dto = new CreateStudentRequest(
            nome_completo: $studentData['nome_completo'],
            cpf: $studentData['cpf'],
            rg: $studentData['rg'] ?? null,
            sexo: Sexo::tryFromNullable($studentData['sexo'] ?? null),
            genero: $studentData['genero'] ?? null,
            foto: $studentData['foto'] ?? null,
            contacts: $contactDTOs,
            addresses: $addressDTOs
        );

        try {
            $student = Services::studentService()->create($dto);
            return $this->created($student->toArray(), 'Aluno criado com sucesso.');
        } catch (DeletedStudentConflictException $e) {
            return $this->respond([
                'status'  => 'conflict',
                'message' => $e->getMessage(),
                'data'    => [
                    'deleted_student_id' => $e->deletedStudentId,
                    'cpf'                => $e->cpf,
                ],
            ], 409);
        }
    }

    public function update($id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->error('ID do aluno é obrigatório.');
        }

        if (!$this->validate(StudentRules::get($id))) {
            return $this->validationError($this->validator->getErrors());
        }

        $studentData = $this->request->getJsonVar('student', true);
        $contacts    = $this->request->getJsonVar('contacts', true) ?? [];
        $addresses   = $this->request->getJsonVar('addresses', true) ?? [];

        $contactDTOs = array_map(fn($c) => new StudentContactRequest(...$c), $contacts);
        $addressDTOs = array_map(fn($a) => new StudentAddressRequest(
            ...[...$a, 'tipo_endereco' => TipoEndereco::from($a['tipo_endereco'] ?? 'residencial')]
        ), $addresses);

        $dto = new UpdateStudentRequest(
            nome_completo: $studentData['nome_completo'],
            cpf: $studentData['cpf'],
            rg: $studentData['rg'] ?? null,
            sexo: Sexo::tryFromNullable($studentData['sexo'] ?? null),
            genero: $studentData['genero'] ?? null,
            foto: $studentData['foto'] ?? null,
            contacts: $contactDTOs,
            addresses: $addressDTOs
        );

        $student = Services::studentService()->update($id, $dto);

        if ($student === null) {
            return $this->notFound('Aluno não encontrado.');
        }

        return $this->success($student->toArray(), 'Aluno atualizado com sucesso.');
    }

    public function delete($id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->error('ID do aluno é obrigatório.');
        }

        $deleted = Services::studentService()->delete($id);

        if (!$deleted) {
            return $this->notFound('Aluno não encontrado.');
        }

        return $this->success(message: 'Aluno excluído com sucesso.');
    }

    public function restore($id = null): ResponseInterface
    {
        if ($id === null) {
            return $this->error('ID do aluno é obrigatório.');
        }

        if (!$this->validate(StudentRules::get($id))) {
            return $this->validationError($this->validator->getErrors());
        }

        $studentData = $this->request->getJsonVar('student', true);
        $contacts    = $this->request->getJsonVar('contacts', true) ?? [];
        $addresses   = $this->request->getJsonVar('addresses', true) ?? [];

        $contactDTOs = array_map(fn($c) => new StudentContactRequest(...$c), $contacts);
        $addressDTOs = array_map(fn($a) => new StudentAddressRequest(
            ...[...$a, 'tipo_endereco' => TipoEndereco::from($a['tipo_endereco'] ?? 'residencial')]
        ), $addresses);

        $dto = new CreateStudentRequest(
            nome_completo: $studentData['nome_completo'],
            cpf: $studentData['cpf'],
            rg: $studentData['rg'] ?? null,
            sexo: Sexo::tryFromNullable($studentData['sexo'] ?? null),
            genero: $studentData['genero'] ?? null,
            foto: $studentData['foto'] ?? null,
            contacts: $contactDTOs,
            addresses: $addressDTOs
        );

        $student = Services::studentService()->restore($id, $dto);

        if ($student === null) {
            return $this->notFound('Aluno deletado não encontrado.');
        }

        return $this->success($student->toArray(), 'Aluno restaurado com sucesso.');
    }
}
