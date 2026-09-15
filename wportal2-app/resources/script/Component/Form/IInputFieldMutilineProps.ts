import { InputBaseComponentProps, SxProps, Theme } from '@mui/material';
import { IPropsBase } from '@/script/System/System';

export type InputFieldMultilineType = 'text' | 'number';
export interface IInputFieldMultilineProps extends IPropsBase {
  inputValue: string;
  onChange: (value: string) => void;
  onBlur?: (value: string) => void;
  visibleError?: boolean;
  errors?: string[];
  placeHolder?: string;
  type?: InputFieldMultilineType;
  rows?: number;
  autoComplete?: string;
  disabled?: boolean;
  fieldSx?: SxProps<Theme>;
  inputProps?: InputBaseComponentProps;
}
