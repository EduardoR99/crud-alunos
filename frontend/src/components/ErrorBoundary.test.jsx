import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import ErrorBoundary from './ErrorBoundary';

function ProblemChild() {
  throw new Error('Erro de teste');
}

describe('ErrorBoundary', () => {
  // Suprimir logs de erro do React no console durante testes
  const originalError = console.error;
  beforeEach(() => {
    console.error = vi.fn();
  });
  afterEach(() => {
    console.error = originalError;
  });

  it('renderiza children quando não há erro', () => {
    render(
      <ErrorBoundary>
        <div>Conteúdo normal</div>
      </ErrorBoundary>
    );
    expect(screen.getByText('Conteúdo normal')).toBeInTheDocument();
  });

  it('renderiza fallback quando filho lança erro', () => {
    render(
      <ErrorBoundary>
        <ProblemChild />
      </ErrorBoundary>
    );
    expect(screen.getByText('Algo deu errado')).toBeInTheDocument();
    expect(screen.getByText('Tentar novamente')).toBeInTheDocument();
    expect(screen.getByText('Recarregar página')).toBeInTheDocument();
  });

  it('reseta o estado ao clicar em "Tentar novamente"', async () => {
    let shouldThrow = true;

    function ConditionalChild() {
      if (shouldThrow) throw new Error('Erro');
      return <div>Recuperado</div>;
    }

    const { rerender } = render(
      <ErrorBoundary>
        <ConditionalChild />
      </ErrorBoundary>
    );

    expect(screen.getByText('Algo deu errado')).toBeInTheDocument();

    shouldThrow = false;
    const user = userEvent.setup();
    await user.click(screen.getByText('Tentar novamente'));

    rerender(
      <ErrorBoundary>
        <ConditionalChild />
      </ErrorBoundary>
    );

    expect(screen.queryByText('Algo deu errado')).not.toBeInTheDocument();
  });
});
