import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import toast from 'react-hot-toast';
import { studentFormSchema } from '../models/studentSchema';
import { useStudentForm } from '../hooks/useStudents';
import { MESSAGES, SEXO_OPTIONS } from '../config/constants';
import FormField, { inputBaseClass } from '../components/FormField';
import MaskedInput from '../components/MaskedInput';
import AddressFields from '../components/AddressFields';
import ContactFields from '../components/ContactFields';
import ConfirmModal from '../components/ConfirmModal';
import PhotoUpload from '../components/PhotoUpload';
import { maskCpf } from '../utils/masks';
import { studentService } from '../services/studentService';

export default function StudentForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const isEditing = !!id;

  const { loading, error, getStudent, saveStudent } = useStudentForm();
  const [loadingData, setLoadingData] = useState(isEditing);
  const [photo, setPhoto] = useState(null);
  const [showRestoreModal, setShowRestoreModal] = useState(false);
  const [deletedStudentId, setDeletedStudentId] = useState(null);
  const [pendingFormData, setPendingFormData] = useState(null);

  const {
    register,
    handleSubmit,
    control,
    setValue,
    reset,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(studentFormSchema),
    defaultValues: {
      student: {
        nome_completo: '',
        cpf: '',
        rg: '',
        sexo: '',
        genero: '',
      },
      contacts: [],
      addresses: [],
    },
  });

  useEffect(() => {
    if (!isEditing) return;

    const loadStudent = async () => {
      const data = await getStudent(Number(id));

      if (data) {
        if (data.foto) {
          setPhoto(data.foto);
        }

        reset({
          student: {
            nome_completo: data.nome_completo || '',
            cpf: data.cpf || '',
            rg: data.rg || '',
            sexo: data.sexo || '',
            genero: data.genero || '',
          },
          contacts: data.contacts?.map((c) => ({
            email: c.email || '',
            telefones: c.telefones || '',
            rede_social: c.rede_social || '',
          })) || [],
          addresses: data.addresses?.map((a) => ({
            cep: a.cep || '',
            logradouro: a.logradouro || '',
            bairro: a.bairro || '',
            cidade: a.cidade || '',
            estado: a.estado || '',
            numero: a.numero || '',
            complemento: a.complemento || '',
            ponto_referencia: a.ponto_referencia || '',
            tipo_endereco: a.tipo_endereco || 'residencial',
          })) || [],
        });
      }

      setLoadingData(false);
    };

    loadStudent();
  }, [id, isEditing, getStudent, reset]);

  const onSubmit = async (data) => {
    const payload = {
      ...data,
      student: { ...data.student, foto: photo || null },
    };
    const result = await saveStudent(payload, isEditing ? Number(id) : null);

    if (result.success) {
      toast.success(isEditing ? MESSAGES.STUDENT_UPDATED : MESSAGES.STUDENT_CREATED);
      navigate('/students');
    } else if (result.conflict) {
      setDeletedStudentId(result.deletedStudentId);
      setPendingFormData(data);
      setShowRestoreModal(true);
    }
  };

  const handleRestore = async () => {
    if (!deletedStudentId || !pendingFormData) return;

    try {
      const restoreData = {
        ...pendingFormData,
        student: { ...pendingFormData.student, foto: photo || null },
      };
      await studentService.restore(deletedStudentId, restoreData);
      toast.success('Aluno restaurado e atualizado com sucesso!');
      navigate('/students');
    } catch (err) {
      const message = err.response?.data?.message || MESSAGES.GENERIC_ERROR;
      toast.error(message);
    }
  };

  if (loadingData) {
    return (
      <div className="text-center py-12 text-gray-500 dark:text-gray-400">
        Carregando dados do aluno...
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        {isEditing ? 'Editar Aluno' : 'Novo Aluno'}
      </h1>

      {error && (
        <div className="mb-6 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-8">
        {/* Dados Pessoais */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Dados Pessoais
          </h2>

          <div className="mb-6">
            <PhotoUpload value={photo} onChange={setPhoto} />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormField
              label="Nome Completo *"
              error={errors?.student?.nome_completo?.message}
              className="md:col-span-2"
            >
              <input
                {...register('student.nome_completo')}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="CPF *"
              error={errors?.student?.cpf?.message}
            >
              <MaskedInput
                name="student.cpf"
                control={control}
                mask={maskCpf}
                placeholder="000.000.000-00"
              />
            </FormField>

            <FormField
              label="RG"
              error={errors?.student?.rg?.message}
            >
              <input
                {...register('student.rg')}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Sexo"
              error={errors?.student?.sexo?.message}
            >
              <select
                {...register('student.sexo')}
                className={inputBaseClass}
              >
                <option value="">Selecione</option>
                {SEXO_OPTIONS.map((opt) => (
                  <option key={opt.value} value={opt.value}>{opt.label}</option>
                ))}
              </select>
            </FormField>

            <FormField
              label="Gênero"
              error={errors?.student?.genero?.message}
            >
              <input
                {...register('student.genero')}
                className={inputBaseClass}
              />
            </FormField>
          </div>
        </div>

        {/* Contatos */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
          <ContactFields
            control={control}
            register={register}
            errors={errors}
          />
        </div>

        {/* Endereços */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
          <AddressFields
            control={control}
            register={register}
            errors={errors}
            setValue={setValue}
          />
        </div>

        {/* Ações */}
        <div className="flex gap-4">
          <button
            type="submit"
            disabled={loading}
            className="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white font-medium rounded-lg transition-colors"
          >
            {loading ? 'Salvando...' : isEditing ? 'Atualizar' : 'Cadastrar'}
          </button>
          <button
            type="button"
            onClick={() => navigate('/students')}
            className="px-6 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium rounded-lg transition-colors"
          >
            Cancelar
          </button>
        </div>
      </form>

      <ConfirmModal
        isOpen={showRestoreModal}
        onClose={() => setShowRestoreModal(false)}
        onConfirm={handleRestore}
        title="Aluno excluído encontrado"
        message="Já existe um aluno excluído com este CPF. Deseja reativá-lo e atualizar com os novos dados?"
        confirmText="Reativar"
        cancelText="Cancelar"
        variant="warning"
      />
    </div>
  );
}
