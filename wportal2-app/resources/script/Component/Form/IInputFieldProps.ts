import { InputBaseComponentProps, SxProps, Theme } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

export type InputFieldType = 'text' | 'email' | 'password' | 'date' | 'number';
export interface IInputFieldProps extends IPropsBase {
  inputValue: string;
  onChange: (value: string) => void;
  onBlur?: (value: string) => void;
  visibleError?: boolean;
  errors?: string[];
  placeHolder?: string;
  type?: InputFieldType;
  autoComplete?: string;
  disabled?: boolean;
  fieldSx?: SxProps<Theme>;
  inputProps?: InputBaseComponentProps & { sx?: SxProps<Theme> };
}
