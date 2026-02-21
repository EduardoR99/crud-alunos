<?php

namespace App\Controllers\Api;

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

        return $this->success(
            data: $result['students'],
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

        return $this->success($student);
    }

    public function create(): ResponseInterface
    {
        if (!$this->validate(StudentRules::get())) {
            return $this->validationError($this->validator->getErrors());
        }

        $studentData = $this->request->getJsonVar('student', true);
        $contacts    = $this->request->getJsonVar('contacts', true) ?? [];
        $addresses   = $this->request->getJsonVar('addresses', true) ?? [];

        $student = Services::studentService()->create($studentData, $contacts, $addresses);

        return $this->created($student, 'Aluno criado com sucesso.');
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

        $student = Services::studentService()->update($id, $studentData, $contacts, $addresses);

        if ($student === null) {
            return $this->notFound('Aluno não encontrado.');
        }

        return $this->success($student, 'Aluno atualizado com sucesso.');
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
}
