import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import PasswordInput from './PasswordInput';

describe('PasswordInput', () => {
  it('renderiza com type password por padrão', () => {
    render(<PasswordInput id="pw" value="" onChange={() => {}} />);
    const input = screen.getByPlaceholderText('••••••');
    expect(input).toHaveAttribute('type', 'password');
  });

  it('alterna para text ao clicar no botão de visibilidade', async () => {
    const user = userEvent.setup();
    render(<PasswordInput id="pw" value="123" onChange={() => {}} />);

    const toggle = screen.getByLabelText('Mostrar senha');
    await user.click(toggle);

    const input = screen.getByPlaceholderText('••••••');
    expect(input).toHaveAttribute('type', 'text');
  });

  it('volta para password ao clicar novamente', async () => {
    const user = userEvent.setup();
    render(<PasswordInput id="pw" value="123" onChange={() => {}} />);

    const toggle = screen.getByLabelText('Mostrar senha');
    await user.click(toggle);

    const toggleHide = screen.getByLabelText('Ocultar senha');
    await user.click(toggleHide);

    const input = screen.getByPlaceholderText('••••••');
    expect(input).toHaveAttribute('type', 'password');
  });

  it('chama onChange ao digitar', async () => {
    const handleChange = vi.fn();
    const user = userEvent.setup();

    render(<PasswordInput id="pw" value="" onChange={handleChange} />);

    const input = screen.getByPlaceholderText('••••••');
    await user.type(input, 'a');

    expect(handleChange).toHaveBeenCalled();
  });

  it('aceita placeholder customizado', () => {
    render(<PasswordInput id="pw" value="" onChange={() => {}} placeholder="Digite aqui" />);
    expect(screen.getByPlaceholderText('Digite aqui')).toBeInTheDocument();
  });
});
