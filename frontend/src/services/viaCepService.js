import axios from 'axios';

export const viaCepService = {
  async fetchAddress(cep) {
    const cleanCep = cep.replace(/\D/g, '');

    if (cleanCep.length !== 8) {
      return null;
    }

    const response = await axios.get(`https://viacep.com.br/ws/${cleanCep}/json/`);

    if (response.data.erro) {
      return null;
    }

    return {
      logradouro: response.data.logradouro || '',
      bairro: response.data.bairro || '',
      cidade: response.data.localidade || '',
      estado: response.data.uf || '',
    };
  },
};
