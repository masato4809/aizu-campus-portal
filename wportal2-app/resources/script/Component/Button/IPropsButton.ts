import { ButtonProps } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { EButtonType } from '@/script/Component/Button/EButtonType';

// ButtonPropsから利用するキー.
type UseButtonPropsKeys =
  | 'type'
  | 'disabled'
  | 'startIcon'
  | 'endIcon'
  | 'onClick'
  | 'onMouseEnter'
  | 'onMouseLeave'
  | 'href';

export interface IPropsButton
  extends IPropsBase,
    Pick<ButtonProps, UseButtonPropsKeys> {
  id?: string;
  buttonType?: EButtonType;
  label?: string;
}
