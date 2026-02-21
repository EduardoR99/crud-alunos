import { useTheme } from '../contexts/ThemeContext';

export default function ThemeSwitch() {
  const { theme, toggleTheme } = useTheme();
  const isDark = theme === 'dark';

  return (
    <button
      onClick={toggleTheme}
      className="relative w-10 h-5 rounded-full transition-colors focus:outline-none bg-gray-300 dark:bg-gray-500"
      aria-label="Alternar tema"
    >
      <span
        className={`absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform ${isDark ? 'translate-x-5' : 'translate-x-0'}`}
      />
    </button>
  );
}
