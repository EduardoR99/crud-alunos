import { useRef } from 'react';
import toast from 'react-hot-toast';

const ACCEPTED_TYPES = 'image/jpeg,image/jpg,image/png,image/webp';
const MAX_SIZE_MB = 2;

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

export default function PhotoUpload({ value, onChange }) {
  const inputRef = useRef(null);

  const handleFileSelect = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > MAX_SIZE_MB * 1024 * 1024) {
      toast.error(`A imagem deve ter no máximo ${MAX_SIZE_MB}MB.`);
      return;
    }

    const base64 = await fileToBase64(file);
    onChange(base64);

    if (inputRef.current) inputRef.current.value = '';
  };

  const handleRemove = () => {
    onChange(null);
    if (inputRef.current) inputRef.current.value = '';
  };

  return (
    <div className="flex items-center gap-6">
      <div className="shrink-0">
        {value ? (
          <img
            src={value}
            alt="Foto do aluno"
            className="w-24 h-24 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600"
          />
        ) : (
          <div className="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-2 border-dashed border-gray-400 dark:border-gray-500">
            <svg className="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
        )}
      </div>

      <div className="flex flex-col gap-2">
        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Foto do Aluno
        </label>

        <div className="flex gap-2">
          <button
            type="button"
            onClick={() => inputRef.current?.click()}
            className="px-3 py-1.5 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors"
          >
            {value ? 'Alterar' : 'Escolher foto'}
          </button>

          {value && (
            <button
              type="button"
              onClick={handleRemove}
              className="px-3 py-1.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 border border-red-300 dark:border-red-600 rounded-lg transition-colors"
            >
              Remover
            </button>
          )}
        </div>

        <p className="text-xs text-gray-500 dark:text-gray-400">
          JPG, PNG ou WebP. Max {MAX_SIZE_MB}MB.
        </p>

        <input
          ref={inputRef}
          type="file"
          accept={ACCEPTED_TYPES}
          onChange={handleFileSelect}
          className="hidden"
        />
      </div>
    </div>
  );
}
