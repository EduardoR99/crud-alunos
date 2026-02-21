export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api';

export const STORAGE_KEYS = {
  THEME: 'theme',
};

export const PAGINATION = {
  PER_PAGE: 15,
};

export const MESSAGES = {
  LOGIN_SUCCESS: 'Login realizado com sucesso!',
  LOGIN_ERROR: 'E-mail ou senha inválidos.',
  SESSION_EXPIRED: 'Sessão expirada. Faça login novamente.',
  STUDENT_CREATED: 'Aluno criado com sucesso!',
  STUDENT_UPDATED: 'Aluno atualizado com sucesso!',
  STUDENT_DELETED: 'Aluno excluído com sucesso!',
  CONFIRM_DELETE: 'Tem certeza que deseja excluir este aluno?',
  GENERIC_ERROR: 'Ocorreu um erro inesperado. Tente novamente.',
};

export const SEXO_OPTIONS = [
  { value: 'M', label: 'Masculino' },
  { value: 'F', label: 'Feminino' },
];

export const TIPO_ENDERECO_OPTIONS = [
  { value: 'residencial', label: 'Residencial' },
  { value: 'comercial', label: 'Comercial' },
  { value: 'outro', label: 'Outro' },
];

export const ESTADOS_BR = [
  'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA',
  'MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN',
  'RS','RO','RR','SC','SP','SE','TO',
];
