import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import PasswordStrength, { isPasswordValid } from './PasswordStrength';

describe('isPasswordValid', () => {
  it('rejeita senha curta', () => {
    expect(isPasswordValid('Ab1!')).toBe(false);
  });

  it('rejeita senha sem maiúscula', () => {
    expect(isPasswordValid('abcdefg1!')).toBe(false);
  });

  it('rejeita senha sem minúscula', () => {
    expect(isPasswordValid('ABCDEFG1!')).toBe(false);
  });

  it('rejeita senha sem número', () => {
    expect(isPasswordValid('Abcdefgh!')).toBe(false);
  });

  it('rejeita senha sem caractere especial', () => {
    expect(isPasswordValid('Abcdefg12')).toBe(false);
  });

  it('aceita senha válida', () => {
    expect(isPasswordValid('Senha@123')).toBe(true);
  });

  it('aceita senha complexa', () => {
    expect(isPasswordValid('M1nh@Senh4Forte!')).toBe(true);
  });
});

describe('PasswordStrength component', () => {
  it('não renderiza quando password está vazio', () => {
    const { container } = render(<PasswordStrength password="" />);
    expect(container.innerHTML).toBe('');
  });

  it('renderiza 5 regras quando password tem valor', () => {
    render(<PasswordStrength password="a" />);
    const items = screen.getAllByRole('listitem');
    expect(items).toHaveLength(5);
  });

  it('marca regra como atendida para senha válida', () => {
    render(<PasswordStrength password="Senha@123" />);
    const items = screen.getAllByRole('listitem');
    items.forEach((item) => {
      expect(item.textContent).toContain('✓');
    });
  });
});
