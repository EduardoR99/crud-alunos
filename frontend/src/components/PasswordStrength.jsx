const rules = [
  { test: (pw) => pw.length >= 8, label: 'Mínimo 8 caracteres' },
  { test: (pw) => /[a-z]/.test(pw), label: 'Uma letra minúscula' },
  { test: (pw) => /[A-Z]/.test(pw), label: 'Uma letra maiúscula' },
  { test: (pw) => /\d/.test(pw), label: 'Um número' },
  { test: (pw) => /[\W_]/.test(pw), label: 'Um caractere especial' },
];

export function isPasswordValid(password) {
  return rules.every((rule) => rule.test(password));
}

export default function PasswordStrength({ password }) {
  if (!password) return null;

  return (
    <ul className="mt-2 space-y-1">
      {rules.map((rule) => {
        const passed = rule.test(password);
        return (
          <li
            key={rule.label}
            className={`text-xs flex items-center gap-1.5 ${
              passed
                ? 'text-emerald-600 dark:text-emerald-400'
                : 'text-gray-400 dark:text-gray-500'
            }`}
          >
            <span>{passed ? '\u2713' : '\u2022'}</span>
            {rule.label}
          </li>
        );
      })}
    </ul>
  );
}
