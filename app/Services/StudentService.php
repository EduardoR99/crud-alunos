<?php

namespace App\Services;

use App\Entities\Student;
use App\Entities\StudentAddress;
use App\Entities\StudentContact;
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

    /**
     * Listagem paginada com prevenção de N+1.
     * 1) Busca alunos paginados (SELECT apenas colunas da tabela)
     * 2) Coleta IDs em um único array
     * 3) Busca contatos e endereços em lote com whereIn (2 queries extras, não N)
     */
    public function list(int $perPage, ?string $search = null): array
    {
        $result = $this->studentModel->paginatedList($perPage, $search);

        /** @var Student[] $students */
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

        return [
            'students'    => $students,
            'currentPage' => $pager->getCurrentPage(),
            'perPage'     => $pager->getPerPage(),
            'total'       => $pager->getTotal(),
            'pageCount'   => $pager->getPageCount(),
        ];
    }

    /**
     * Busca um aluno com todos os relacionamentos.
     */
    public function findById(int $id): ?Student
    {
        $student = $this->studentModel->find($id);

        if ($student === null) {
            return null;
        }

        $student->contacts  = $this->contactModel->where('student_id', $id)->findAll();
        $student->addresses = $this->addressModel->where('student_id', $id)->findAll();

        return $student;
    }

    /**
     * Cria aluno + contatos + endereços em transação atômica.
     *
     * @param array<string, mixed> $studentData
     * @param array<int, array<string, mixed>> $contacts
     * @param array<int, array<string, mixed>> $addresses
     */
    public function create(array $studentData, array $contacts, array $addresses): Student
    {
        $this->db->transStart();

        $student = new Student($studentData);
        $this->studentModel->insert($student);
        $studentId = (int) $this->studentModel->getInsertID();

        $this->saveRelations($studentId, $contacts, $addresses);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Falha ao criar aluno.');
        }

        return $this->findById($studentId);
    }

    /**
     * Atualiza aluno + contatos + endereços em transação atômica.
     * Estratégia: delete + re-insert para contatos/endereços (simples e seguro).
     *
     * @param array<string, mixed> $studentData
     * @param array<int, array<string, mixed>> $contacts
     * @param array<int, array<string, mixed>> $addresses
     */
    public function update(int $id, array $studentData, array $contacts, array $addresses): ?Student
    {
        $existing = $this->studentModel->find($id);

        if ($existing === null) {
            return null;
        }

        $this->db->transStart();

        $this->studentModel->update($id, $studentData);

        $this->contactModel->hardDeleteByStudentId($id);
        $this->addressModel->hardDeleteByStudentId($id);

        $this->saveRelations($id, $contacts, $addresses);

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

    /**
     * Salva contatos e endereços vinculados ao student_id.
     *
     * @param array<int, array<string, mixed>> $contacts
     * @param array<int, array<string, mixed>> $addresses
     */
    private function saveRelations(int $studentId, array $contacts, array $addresses): void
    {
        foreach ($contacts as $contactData) {
            $contact = new StudentContact(array_merge($contactData, ['student_id' => $studentId]));
            $this->contactModel->insert($contact);
        }

        foreach ($addresses as $addressData) {
            $address = new StudentAddress(array_merge($addressData, ['student_id' => $studentId]));
            $this->addressModel->insert($address);
        }
    }
}
