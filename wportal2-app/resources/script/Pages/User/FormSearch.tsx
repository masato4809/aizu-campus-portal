import * as React from 'react';
import { Box, Checkbox, FormControl } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { InputField } from '@/script/Component/Form/InputField';
import { ButtonGeneral } from '@/script/Component/Button/ButtonGeneral';
import { TypoText } from '@/script/Component/Typography/TypoText';
import { responsiveSize, responsiveSpacing } from '@/script/System/Responsive';

export interface IFormSearch {
  keyword: string;
  inactiveUserFlag: boolean;
}
export const initialValue: IFormSearch = {
  keyword: '',
  inactiveUserFlag: false,
};

interface IProps extends IPropsBase {
  formSearch: IFormSearch;
  handleUpdate: (newValues: Partial<IFormSearch>) => void;
  handleSubmit: () => void;
}
export const FormSearch: React.FC<IProps> = ({
  sx,
  formSearch,
  handleUpdate,
  handleSubmit,
}) => {
  return (
    <FormControl
      sx={{
        width: '100%',
        gap: responsiveSpacing(4),
        ...sx,
      }}
    >
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>キーワード</TypoText>
        <InputField
          sx={{
            width: '100%',
          }}
          inputProps={{
            sx: {
              width: '100%',
            },
          }}
          id="user-search"
          inputValue={formSearch.keyword}
          onChange={keyword => handleUpdate({ keyword })}
        />
      </Box>
      <Box
        sx={{
          display: 'flex',
          alignItems: 'center',
        }}
      >
        <TypoText sx={{ minWidth: responsiveSize(200) }}>
          非アクティブのみ
        </TypoText>
        <Checkbox
          checked={formSearch.inactiveUserFlag}
          onChange={v => handleUpdate({ inactiveUserFlag: v.target.checked })}
        />
      </Box>
      <ButtonGeneral
        sx={{
          marginLeft: 'auto',
          width: responsiveSize(200),
        }}
        label="検索"
        onClick={handleSubmit}
      />
    </FormControl>
  );
};
