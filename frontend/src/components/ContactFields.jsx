import { useFieldArray } from 'react-hook-form';
import FormField, { inputBaseClass } from './FormField';
import MaskedInput from './MaskedInput';
import { EMPTY_CONTACT } from '../models/studentSchema';
import { maskPhone } from '../utils/masks';

export default function ContactFields({ control, register, errors }) {
  const { fields, append, remove } = useFieldArray({
    control,
    name: 'contacts',
  });

  return (
    <div>
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
          Contatos
        </h3>
        <button
          type="button"
          onClick={() => append(EMPTY_CONTACT)}
          className="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
        >
          + Adicionar
        </button>
      </div>

      {fields.length === 0 && (
        <p className="text-sm text-gray-500 dark:text-gray-400 italic">
          Nenhum contato adicionado.
        </p>
      )}

      {fields.map((field, index) => (
        <div
          key={field.id}
          className="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4"
        >
          <div className="flex justify-between items-center mb-3">
            <span className="text-sm font-medium text-gray-600 dark:text-gray-400">
              Contato {index + 1}
            </span>
            <button
              type="button"
              onClick={() => remove(index)}
              className="text-sm text-red-600 dark:text-red-400 hover:underline"
            >
              Remover
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
            <FormField
              label="E-mail"
              error={errors?.contacts?.[index]?.email?.message}
            >
              <input
                type="email"
                {...register(`contacts.${index}.email`)}
                placeholder="email@exemplo.com"
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Telefone(s)"
              error={errors?.contacts?.[index]?.telefones?.message}
            >
              <MaskedInput
                name={`contacts.${index}.telefones`}
                control={control}
                mask={maskPhone}
                placeholder="(00) 00000-0000"
              />
            </FormField>

            <FormField
              label="Rede Social"
              error={errors?.contacts?.[index]?.rede_social?.message}
            >
              <input
                {...register(`contacts.${index}.rede_social`)}
                placeholder="@usuario"
                className={inputBaseClass}
              />
            </FormField>
          </div>
        </div>
      ))}
    </div>
  );
}
