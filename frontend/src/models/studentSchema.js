import { z } from 'zod';

const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
const cepRegex = /^\d{5}-?\d{3}$/;
const phoneRegex = /^\(\d{2}\)\s?\d{4,5}-?\d{4}$/;
const socialMediaRegex = /^@?[a-zA-Z0-9._]{1,30}$/;

const contactSchema = z.object({
  email: z
    .string()
    .max(255, 'E-mail deve ter no máximo 255 caracteres')
    .email('E-mail inválido')
    .or(z.literal(''))
    .optional(),
  telefones: z
    .string()
    .refine(
      (val) => !val || phoneRegex.test(val),
      'Telefone inválido (formato: (00) 00000-0000 ou (00) 0000-0000)'
    )
    .optional(),
  rede_social: z
    .string()
    .refine(
      (val) => !val || socialMediaRegex.test(val),
      'Usuário de rede social inválido (apenas letras, números, . e _)'
    )
    .optional(),
});

const addressSchema = z.object({
  cep: z
    .string()
    .regex(cepRegex, 'CEP inválido (formato: 00000-000)')
    .or(z.literal('')),
  logradouro: z
    .string()
    .max(255, 'Logradouro deve ter no máximo 255 caracteres'),
  bairro: z
    .string()
    .max(150, 'Bairro deve ter no máximo 150 caracteres'),
  cidade: z
    .string()
    .max(150, 'Cidade deve ter no máximo 150 caracteres'),
  estado: z
    .string()
    .length(2, 'Estado deve ter 2 caracteres')
    .or(z.literal('')),
  numero: z
    .string()
    .max(20, 'Número deve ter no máximo 20 caracteres'),
  complemento: z
    .string()
    .max(255, 'Complemento deve ter no máximo 255 caracteres')
    .optional(),
  ponto_referencia: z
    .string()
    .max(255, 'Ponto de referência deve ter no máximo 255 caracteres')
    .optional(),
  tipo_endereco: z
    .enum(['residencial', 'comercial', 'outro'])
    .default('residencial'),
});

export const studentFormSchema = z.object({
  student: z.object({
    nome_completo: z
      .string()
      .min(3, 'Nome deve ter pelo menos 3 caracteres')
      .max(255, 'Nome deve ter no máximo 255 caracteres'),
    cpf: z
      .string()
      .regex(cpfRegex, 'CPF inválido (formato: 000.000.000-00)'),
    rg: z
      .string()
      .max(20, 'RG deve ter no máximo 20 caracteres')
      .optional(),
    sexo: z
      .enum(['M', 'F', ''])
      .optional(),
    genero: z
      .string()
      .max(50, 'Gênero deve ter no máximo 50 caracteres')
      .optional(),
  }),
  contacts: z.array(contactSchema).default([]),
  addresses: z.array(addressSchema).default([]),
});

export const EMPTY_CONTACT = {
  email: '',
  telefones: '',
  rede_social: '',
};

export const EMPTY_ADDRESS = {
  cep: '',
  logradouro: '',
  bairro: '',
  cidade: '',
  estado: '',
  numero: '',
  complemento: '',
  ponto_referencia: '',
  tipo_endereco: 'residencial',
};
