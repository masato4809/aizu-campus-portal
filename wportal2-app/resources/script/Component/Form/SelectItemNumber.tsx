import * as React from 'react';
import {
  Box,
  MenuItem,
  Select,
  SelectChangeEvent,
  SxProps,
  Theme,
} from '@mui/material';
import { useEffect } from 'react';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { Closable } from '@/script/Component/Misc/Closable';
import { InputError } from '@/script/Component/Form/InputError';
import { E_COLOR } from '@/script/Enum/EColor';
import { IPropsBase } from '@/script/System/System';

export interface IItemNumber {
  id: number;
  label: string;
}

interface IProps extends IPropsBase {
  value: number;
  list: IItemNumber[];
  onChange: (value: number) => void;
  errors?: string[];
  visibleError?: boolean;
  sxSelect?: SxProps<Theme>;
}

export const SelectItemNumber: React.FC<IProps> = ({
  sx,
  value,
  list,
  onChange,
  errors,
  visibleError,
  sxSelect,
}) => {
  const [isEdited, setIsEdited] = React.useState<boolean>(false);

  /**
   * ロード時に編集フラグを落とす.
   */
  useEffect(() => {
    setIsEdited(false);
  }, [errors]);

  /**
   * 背景色.
   */
  const getBackgroundColor = (): string => {
    if (errors === undefined || errors.length === 0) {
      return E_COLOR.WHITE;
    }

    return !isEdited ? E_COLOR.RED_LIGHT : E_COLOR.WHITE;
  };

  /**
   * 選択フィールドが更新された時.
   */
  const handleChangeSelect = (
    event: SelectChangeEvent<number>,
    _child?: React.ReactNode,
  ): void => {
    setIsEdited(true);
    onChange(Number(event.target.value));
  };

  return (
    <Box
      sx={{
        ...sx,
      }}
    >
      <Select<number>
        sx={{
          backgroundColor: getBackgroundColor(),
          ...sxSelect,
        }}
        value={value}
        onChange={handleChangeSelect}
        displayEmpty
      >
        {list.map(item => (
          <MenuItem value={item.id} key={item.id}>
            <TypoText>{item.label}</TypoText>
          </MenuItem>
        ))}
      </Select>
      <Closable open={visibleError}>
        <InputError sx={{ marginLeft: '0' }} errors={errors} />
      </Closable>
    </Box>
  );
};
