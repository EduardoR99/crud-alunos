<?php

namespace App\Services;

use App\DTOs\Request\CreateStudentRequest;
use App\DTOs\Request\UpdateStudentRequest;
use App\DTOs\Response\StudentResponse;
use App\Entities\Student;
use App\Entities\StudentAddress;
use App\Entities\StudentContact;
use App\Exceptions\DeletedStudentConflictException;
use App\Models\StudentAddressModel;
use App\Models\StudentContactModel;
use App\Models\StudentModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class StudentService
{
    private BaseConnection $db;

    public function __construct(
        private readonly StudentModel $studentModel,
        private readonly StudentContactModel $contactModel,
        private readonly StudentAddressModel $addressModel,
    ) {
        $this->db = db_connect();
    }

    public function list(int $perPage, ?string $search = null): array
    {
        $result = $this->studentModel->paginatedList($perPage, $search);

        $students = $result['data'];
        $pager    = $result['pager'];

        if (!empty($students)) {
            $ids = array_map(static fn(Student $s): int => $s->id, $students);

            $contactsByStudent = $this->contactModel->findByStudentIds($ids);
            $addressesByStudent = $this->addressModel->findByStudentIds($ids);

            foreach ($students as $student) {
                $student->contacts  = $contactsByStudent[$student->id] ?? [];
                $student->addresses = $addressesByStudent[$student->id] ?? [];
            }
        }

        $studentDTOs = array_map(fn($s) => StudentResponse::fromEntity($s), $students);

        return [
            'students'    => $studentDTOs,
            'currentPage' => $pager->getCurrentPage(),
            'perPage'     => $pager->getPerPage(),
            'total'       => $pager->getTotal(),
            'pageCount'   => $pager->getPageCount(),
        ];
    }

    public function findById(int $id): ?StudentResponse
    {
        $student = $this->studentModel->find($id);

        if ($student === null) {
            return null;
        }

        $student->contacts  = $this->contactModel->where('student_id', $id)->findAll();
        $student->addresses = $this->addressModel->where('student_id', $id)->findAll();

        return StudentResponse::fromEntity($student);
    }

    public function create(CreateStudentRequest $dto): StudentResponse
    {
        $deletedStudent = $this->studentModel->findDeletedByCpf($dto->cpf);

        if ($deletedStudent !== null) {
            throw new DeletedStudentConflictException($deletedStudent->id, $dto->cpf);
        }

        $this->db->transStart();

        $student = new Student([
            'nome_completo' => $dto->nome_completo,
            'cpf'           => $dto->cpf,
            'rg'            => $dto->rg,
            'sexo'          => $dto->sexo?->value,
            'genero'        => $dto->genero,
            'foto'          => $dto->foto,
        ]);

        $this->studentModel->insert($student);
        $studentId = (int) $this->studentModel->getInsertID();

        $this->saveRelations($studentId, $dto->contacts, $dto->addresses);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Falha ao criar aluno.');
        }

        return $this->findById($studentId);
    }

    public function update(int $id, UpdateStudentRequest $dto): ?StudentResponse
    {
        $existing = $this->studentModel->find($id);

        if ($existing === null) {
            return null;
        }

        $this->db->transStart();

        $this->studentModel->update($id, [
            'nome_completo' => $dto->nome_completo,
            'cpf'           => $dto->cpf,
            'rg'            => $dto->rg,
            'sexo'          => $dto->sexo?->value,
            'genero'        => $dto->genero,
            'foto'          => $dto->foto,
        ]);

        $this->contactModel->hardDeleteByStudentId($id);
        $this->addressModel->hardDeleteByStudentId($id);

        $this->saveRelations($id, $dto->contacts, $dto->addresses);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Falha ao atualizar aluno.');
        }

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $existing = $this->studentModel->find($id);

        if ($existing === null) {
            return false;
        }

        $this->db->transStart();

        $this->contactModel->deleteByStudentId($id);
        $this->addressModel->deleteByStudentId($id);
        $this->studentModel->delete($id);

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    public function restore(int $id, CreateStudentRequest $dto): ?StudentResponse
    {
        $deletedStudent = $this->studentModel->onlyDeleted()->find($id);

        if ($deletedStudent === null) {
            return null;
        }

        $this->db->transStart();

        $this->db->table('students')
            ->where('id', $id)
            ->update([
                'nome_completo' => $dto->nome_completo,
                'cpf'           => $dto->cpf,
                'rg'            => $dto->rg,
                'sexo'          => $dto->sexo?->value,
                'genero'        => $dto->genero,
                'foto'          => $dto->foto,
                'deleted_at'    => null,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

        $this->contactModel->hardDeleteByStudentId($id);
        $this->addressModel->hardDeleteByStudentId($id);

        $this->saveRelations($id, $dto->contacts, $dto->addresses);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Falha ao restaurar aluno.');
        }

        return $this->findById($id);
    }

    private function saveRelations(int $studentId, array $contacts, array $addresses): void
    {
        foreach ($contacts as $contactDto) {
            $contact = new StudentContact([
                'student_id'  => $studentId,
                'email'       => $contactDto->email,
                'telefones'   => $contactDto->telefones,
                'rede_social' => $contactDto->rede_social,
            ]);
            $this->contactModel->insert($contact);
        }

        foreach ($addresses as $addressDto) {
            $address = new StudentAddress([
                'student_id'        => $studentId,
                'cep'               => $addressDto->cep,
                'logradouro'        => $addressDto->logradouro,
                'bairro'            => $addressDto->bairro,
                'cidade'            => $addressDto->cidade,
                'estado'            => $addressDto->estado,
                'numero'            => $addressDto->numero,
                'complemento'       => $addressDto->complemento,
                'ponto_referencia'  => $addressDto->ponto_referencia,
                'tipo_endereco'     => $addressDto->tipo_endereco->value,
            ]);
            $this->addressModel->insert($address);
        }
    }
}
