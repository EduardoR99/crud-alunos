import { useController } from 'react-hook-form';
import { inputBaseClass } from './FormField';

export default function MaskedInput({ name, control, mask, className, onBlur, ...rest }) {
  const { field } = useController({ name, control });

  const handleChange = (e) => {
    field.onChange(mask(e.target.value));
  };

  const handleBlur = (e) => {
    field.onBlur(e);
    onBlur?.(e);
  };

  return (
    <input
      {...rest}
      ref={field.ref}
      name={field.name}
      value={field.value ?? ''}
      onChange={handleChange}
      onBlur={handleBlur}
      className={className || inputBaseClass}
    />
  );
}
