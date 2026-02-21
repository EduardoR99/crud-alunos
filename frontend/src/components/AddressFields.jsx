import { useFieldArray } from 'react-hook-form';
import FormField, { inputBaseClass } from './FormField';
import MaskedInput from './MaskedInput';
import { EMPTY_ADDRESS } from '../models/studentSchema';
import { TIPO_ENDERECO_OPTIONS, ESTADOS_BR } from '../config/constants';
import { viaCepService } from '../services/viaCepService';
import { maskCep } from '../utils/masks';

export default function AddressFields({ control, register, errors, setValue }) {
  const { fields, append, remove } = useFieldArray({
    control,
    name: 'addresses',
  });

  const handleCepBlur = async (index) => {
    const cep = control._formValues.addresses[index]?.cep;
    if (!cep) return;

    const address = await viaCepService.fetchAddress(cep);
    if (!address) return;

    setValue(`addresses.${index}.logradouro`, address.logradouro);
    setValue(`addresses.${index}.bairro`, address.bairro);
    setValue(`addresses.${index}.cidade`, address.cidade);
    setValue(`addresses.${index}.estado`, address.estado);
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
          Endereços
        </h3>
        <button
          type="button"
          onClick={() => append(EMPTY_ADDRESS)}
          className="px-3 py-1 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
        >
          + Adicionar
        </button>
      </div>

      {fields.length === 0 && (
        <p className="text-sm text-gray-500 dark:text-gray-400 italic">
          Nenhum endereço adicionado.
        </p>
      )}

      {fields.map((field, index) => (
        <div
          key={field.id}
          className="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4"
        >
          <div className="flex justify-between items-center mb-3">
            <span className="text-sm font-medium text-gray-600 dark:text-gray-400">
              Endereço {index + 1}
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
              label="CEP"
              error={errors?.addresses?.[index]?.cep?.message}
            >
              <MaskedInput
                name={`addresses.${index}.cep`}
                control={control}
                mask={maskCep}
                onBlur={() => handleCepBlur(index)}
                placeholder="00000-000"
              />
            </FormField>

            <FormField
              label="Logradouro"
              error={errors?.addresses?.[index]?.logradouro?.message}
              className="md:col-span-2"
            >
              <input
                {...register(`addresses.${index}.logradouro`)}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Número"
              error={errors?.addresses?.[index]?.numero?.message}
            >
              <input
                {...register(`addresses.${index}.numero`)}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Complemento"
              error={errors?.addresses?.[index]?.complemento?.message}
            >
              <input
                {...register(`addresses.${index}.complemento`)}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Bairro"
              error={errors?.addresses?.[index]?.bairro?.message}
            >
              <input
                {...register(`addresses.${index}.bairro`)}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Cidade"
              error={errors?.addresses?.[index]?.cidade?.message}
            >
              <input
                {...register(`addresses.${index}.cidade`)}
                className={inputBaseClass}
              />
            </FormField>

            <FormField
              label="Estado"
              error={errors?.addresses?.[index]?.estado?.message}
            >
              <select
                {...register(`addresses.${index}.estado`)}
                className={inputBaseClass}
              >
                <option value="">Selecione</option>
                {ESTADOS_BR.map((uf) => (
                  <option key={uf} value={uf}>{uf}</option>
                ))}
              </select>
            </FormField>

            <FormField
              label="Tipo"
              error={errors?.addresses?.[index]?.tipo_endereco?.message}
            >
              <select
                {...register(`addresses.${index}.tipo_endereco`)}
                className={inputBaseClass}
              >
                {TIPO_ENDERECO_OPTIONS.map((opt) => (
                  <option key={opt.value} value={opt.value}>{opt.label}</option>
                ))}
              </select>
            </FormField>

            <FormField
              label="Ponto de Referência"
              error={errors?.addresses?.[index]?.ponto_referencia?.message}
              className="md:col-span-3"
            >
              <input
                {...register(`addresses.${index}.ponto_referencia`)}
                className={inputBaseClass}
              />
            </FormField>
          </div>
        </div>
      ))}
    </div>
  );
}
